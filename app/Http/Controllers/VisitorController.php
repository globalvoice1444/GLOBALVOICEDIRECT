<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function recentlyaddedmemeber(Request $request)
    {
        $namesarr = array('Lida', 'Muoi', 'Melody', 'Lavina', 'Rolando', 'Demetrice', 'Lashaun', 'Johnetta', 'Judie', 'Leisha', 'Davina', 'Tiffaney', 'Shelly', 'Vance', 'Kerri', 'Tisa', 'Rocio', 'Wilhemina', 'Maryellen', 'Zackary', 'Linsey', 'Sheron', 'Andria', 'Roselia', 'Gennie', 'Gavin', 'Tambra', 'Ashleigh', 'Mirian', 'Daphne', 'Maximina', 'Perla', 'Yee', 'Dorethea', 'Reda', 'Hal', 'Renee', 'Benny', 'Roselee', 'Von', 'Shannon', 'Kiyoko', 'Zina', 'Yolonda', 'Riva', 'Roseanne', 'Noma', 'Carol', 'Marx', 'Margert', 'Mee', 'Dreama', 'Latasha', 'Mercedes', 'Lamont', 'Diana', 'Terri', 'Rosaria', 'Alice', 'Nia', 'Janella', 'Agripina', 'Jayme', 'Kitty', 'Svetlana', 'Julio', 'Tricia', 'Emerson', 'Nery', 'Helene', 'Elaina', 'Tilda', 'Belen', 'Nita', 'Abby', 'Noma', 'Peggie', 'Stuart', 'Owen', 'Vernie', 'Jeanette', 'Maryln', 'Cleo', 'Dia', 'Felisa', 'Rose', 'Mario', 'Gilberte', 'Frank', 'Magaly', 'Bonita', 'Tanna', 'Jeanne', 'Vernetta', 'Rosanna', 'Lavonne', 'Renay', 'Samual', 'Fatima', 'Naomi');

        $citiesarr = array('Akron, OH', 'Albany, NY', 'Albuquerque, NM', 'Ann Arbor, MI', 'Baton Rouge, LA', 'Allentown, PA', 'Atlanta, GA', 'Baltimore, MD', 'Austin, TX', 'Baton Rouge, LA', 'Birmingham, AL', 'Bridgeport, CT', 'Canton, OH', 'Charlotte, NC', 'Chattanooga, TN', 'Cleveland, OH', 'Daytona Beach, FL', 'Dallas, TX', 'Denver, CO', 'Detroit, MI', 'El Paso, TX', 'Flint, MIFort', 'Lauderdale, FL', 'Fort Myers, FL', 'Fort Worth, TX', 'Gary, IN', 'Grand Rapids, MI', 'Greensboro, NC', 'Greenville, SC', 'Harrisburg, PA', 'Hartford, CT', 'Honolulu, HI', 'Houston, TX', 'Indianapolis, IN', 'Jacksonville, FL', 'Jersey City, NJ', 'Kalamazoo, MI', 'Kansas City, MO', 'Knoxville, TN', 'Lakeland, FL', 'Lancaster, PA', 'Lansing, MI', 'Little Rock, AR', 'Long Beach, CA', 'Los Angeles, CA', 'Louisville, KY', 'Melbourne, FL', 'Memphis, TN', 'Miami, LM', 'ilwaukee, WI', 'Minneapolis, MN', 'Mobile, AL', 'Modesto, CA', 'Nashville, TN', 'New Orleans, LA', 'New York, NY', 'Newark, NJ', 'Newport News, VA', 'Norfolk, VA', 'Oakland, CA', 'Orlando, FL', 'Pensacola, FL', 'Phoenix, AZ', 'Pittsburgh, PA', 'Portland, OR', 'Providence, RI', 'Raleigh, NC', 'Reading, PA', 'Richmond, VA', 'Riverside, CA', 'Rockford, IL', 'Sacramento, CA', 'Saginaw, MI' ,'Saint Louis, MO', 'Salinas, CA', 'Salt Lake City, UT', 'San Antonio, TX', 'San Bernardino, CA', 'San Diego, CA', 'San Francisco, CA', 'San Jose, CA', 'Santa Barbara, CA', 'Santa Rosa, CA', 'Sarasota, FL','Seattle, WA', 'Shreveport, LA', 'Stockton, CA', 'Syracuse, NY', 'Tacoma, WA', 'Tampa, FL', 'Toledo, OH', 'Trenton, NJ', 'Tucson, AZ', 'Vallejo, CA', 'Virginia Beach, VA', 'Washington, DC', 'West Palm Beach, FL', 'Wilmington, DE', 'Worcester, MA', 'York, PA', 'Youngstown, OH');

        $daysarr = array('1 day ago', '2 days ago', '3 days ago', '4 days ago', '5 days ago', '6 days ago', '7 days ago', '1 hour ago', '2 hours ago', '3 hours ago', '4 hours ago', '6 hours ago', '12 hours ago', '14 hours ago', '18 hours ago', '20 hours ago', '23 hours ago', '15 hours ago', '20 minutes ago', '15 minutes ago', '10 minutes ago', '45 minutes ago', '32 minutes ago', '17 minutes ago');

        $randomFloat = rand(0, 49);

        $randomFloat1 = rand(0, sizeof($citiesarr)-1);

        $name = $namesarr[$randomFloat];

        $days = $daysarr[rand(0, sizeof($daysarr)-1)];

        $citystate = $citiesarr[$randomFloat1];

        $returnarr = array($name." from ".$citystate, $days);

        echo json_encode($returnarr);
        die();
    }
}
