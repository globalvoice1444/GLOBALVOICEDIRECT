<?php

namespace App\Http\Controllers;

use App\Support\AiConnect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * 2026-11-02 Global Voice Direct AI Connect: MCP server (Streamable HTTP, JSON-RPC 2.0, stateless, JSON responses).
 * Auth: Authorization: Bearer gvd_live_... (customer API key). No session, no CSRF.
 */
class McpController extends Controller
{
    const PROTOCOLS = ['2025-06-18', '2025-03-26', '2024-11-05'];

    private function rpc($id, $result) { return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result], 200, ['Cache-Control' => 'no-store']); }
    private function rpcErr($id, int $code, string $msg, int $http = 200) { return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $msg]], $http, ['Cache-Control' => 'no-store']); }

    public function handle(Request $req)
    {
        if ($req->isMethod('get')) return response('', 405, ['Allow' => 'POST']);            // no server-initiated stream
        if ($req->isMethod('delete')) return response('', 405, ['Allow' => 'POST']);
        $ip = $req->ip();
        if (RateLimiter::tooManyAttempts('mcp-ip:' . $ip, 120)) return $this->rpcErr(null, -32000, 'Too many requests. Slow down.', 429);
        RateLimiter::hit('mcp-ip:' . $ip, 60);

        $tok = (string) $req->bearerToken();
        $key = $tok !== '' ? AiConnect::findKey($tok) : null;
        if (!$key && $tok !== '') $key = \App\Support\AiOAuth::findAccess($tok); // 2026-11-02 oauth (null when the flag is off)
        if (!$key) {
            $www = 'Bearer realm="Global Voice Direct"';
            if (\App\Support\AiOAuth::on()) $www .= ', resource_metadata="' . \App\Support\AiOAuth::base() . '/.well-known/oauth-protected-resource"' . ($tok !== '' ? ', error="invalid_token"' : '');
            return response()->json(['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32001, 'message' => 'Missing or invalid API key. Create one in your Global Voice Direct panel under AI Connect.']], 401,
                ['WWW-Authenticate' => $www, 'Cache-Control' => 'no-store']);
        }
        $uid = (int) $key->user_id;
        if (!AiConnect::platformOpenFor($uid)) return $this->rpcErr(null, -32003, 'AI Connect is not available yet.', 403);
        if (!AiConnect::userEnabled($uid)) { AiConnect::log($uid, $key->id, 'auth', null, null, 'denied', 'AI Connect is turned off for this account', $ip); return $this->rpcErr(null, -32003, 'AI Connect is turned off for this account. Turn it on in your panel under AI Connect.', 403); }

        $msg = json_decode($req->getContent(), true);
        if (!is_array($msg)) return $this->rpcErr(null, -32700, 'Parse error', 400);
        if (array_is_list($msg)) return $this->rpcErr(null, -32600, 'Batch requests are not supported.', 400);
        $id = $msg['id'] ?? null; $method = (string) ($msg['method'] ?? ''); $p = (array) ($msg['params'] ?? []);
        if (($msg['jsonrpc'] ?? '') !== '2.0' || $method === '') return $this->rpcErr($id, -32600, 'Invalid request');
        if (!array_key_exists('id', $msg)) return response('', 202); // notification (e.g. notifications/initialized)
        DB::table('ai_keys')->where('id', $key->id)->update(['last_used_at' => now()]);
        $scopes = AiConnect::scopes($key);

        switch ($method) {
            case 'initialize':
                $want = (string) ($p['protocolVersion'] ?? '');
                AiConnect::log($uid, $key->id, 'initialize', null, null, 'ok', mb_substr((string) ($p['clientInfo']['name'] ?? 'client'), 0, 60), $ip);
                return $this->rpc($id, ['protocolVersion' => in_array($want, self::PROTOCOLS, true) ? $want : self::PROTOCOLS[0],
                    'capabilities' => ['tools' => ['listChanged' => false]],
                    'serverInfo' => ['name' => 'global-voice-direct', 'title' => 'Global Voice Direct', 'version' => '1.0.0'],
                    'instructions' => 'Tools for this Global Voice Direct account. Actions that send, add or call return a preview first; show it to the user and only call again with confirm=true after they agree.']);
            case 'ping':
                return $this->rpc($id, new \stdClass());
            case 'tools/list':
                $tools = array_values(array_map(function ($t) { unset($t['scope']); return $t; }, array_filter(AiConnect::tools(), fn ($t) => in_array($t['scope'], $scopes, true))));
                return $this->rpc($id, ['tools' => $tools]);
            case 'tools/call':
                $name = (string) ($p['name'] ?? ''); $args = is_array($p['arguments'] ?? null) ? $p['arguments'] : [];
                $def = collect(AiConnect::tools())->firstWhere('name', $name);
                if (!$def) return $this->rpcErr($id, -32602, 'Unknown tool: ' . mb_substr($name, 0, 40));
                if (!in_array($def['scope'], $scopes, true)) { AiConnect::log($uid, $key->id, 'tools/call', $name, null, 'denied', 'key lacks ' . $def['scope'], $ip); return $this->rpc($id, $this->content(true, 'This API key is not allowed to use ' . $name . '. Edit the key in AI Connect.')); }
                if (AiConnect::usedToday($key->id) >= (int) $key->daily_limit) { AiConnect::log($uid, $key->id, 'tools/call', $name, null, 'limited', 'daily limit', $ip); return $this->rpc($id, $this->content(true, 'This API key reached its daily limit of ' . $key->daily_limit . ' actions. It resets at midnight Eastern.')); }
                try { [$isErr, $text, $sum, $mode] = AiConnect::call($uid, $name, $args); }
                catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('mcp tool error', ['tool' => $name, 'e' => mb_substr($e->getMessage(), 0, 150)]); [$isErr, $text, $sum, $mode] = [true, 'Something went wrong. Please try again.', 'error', null]; }
                AiConnect::log($uid, $key->id, 'tools/call', $name, $mode, $isErr ? 'error' : 'ok', $sum, $ip);
                return $this->rpc($id, $this->content($isErr, $text));
        }
        return $this->rpcErr($id, -32601, 'Method not found');
    }

    private function content(bool $isErr, string $text): array { return ['content' => [['type' => 'text', 'text' => $text]], 'isError' => $isErr]; }
}
