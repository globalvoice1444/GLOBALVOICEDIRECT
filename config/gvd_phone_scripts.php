<?php
/*
 | Phone script template library (Phone Scripts page).
 | key => [title, use-for, template, industry group]. Placeholders: {business}, {hours}, {departments_menu}, {departments}.
 | Single source of truth so a future admin-side editor/upload can replace or extend this list.
 | No prices. Keep wording generic.
 */
return [
    'scripts' => [
        'main' => ['Main greeting / auto-attendant', 'Business hours', 'Thank you for calling {business}. {departments_menu} To reach an extension directly, dial it at any time, or stay on the line for the next available representative.', 'General'],
        'after' => ['After-hours', 'Evenings & weekends', "You've reached {business}. Our office is open {hours}. Please leave a message and we'll return your call the next business day.", 'General'],
        'holiday' => ['Holiday hours', 'Holidays', 'Thank you for calling {business}. Our office is closed for the holiday and will reopen on the next business day. Our regular hours are {hours}. Please leave a message and we will call you back.', 'General'],
        'hold' => ['On-hold message', 'Callers on hold', 'Thanks for holding. Your call is important to us at {business}. A representative will be with you shortly.', 'General'],
        'voicemail' => ['Voicemail greeting', 'Extension voicemail', "Hi, you've reached {business}. We're away from our desks right now. Please leave your name, number and a short message, and we'll call you back.", 'General', 'General'],
        'busy' => ['Call-forwarding / busy', 'All agents busy', 'All of our representatives at {business} are currently helping other callers. Please stay on the line, or press 1 to leave a message and we will call you back shortly.', 'General'],

        'lunch' => ['Lunch break', 'Midday closure', "Thank you for calling {business}. Our team is currently out for lunch and will return shortly. Please leave your name, number and a brief message, and we'll call you back as soon as we return. Our regular hours are {hours}.", 'General', 'General'],
        'emergency_closure' => ['Emergency / weather closure', 'Unplanned closures', 'Thank you for calling {business}. Due to an unexpected situation, including severe weather, our office is temporarily closed today. We apologize for any inconvenience. Please leave a message and we will return your call as soon as we reopen. For updates, please visit our website.', 'General'],
        'moving' => ['Temporarily closed / relocating', 'Moves & renovations', "Thank you for calling {business}. Our office is temporarily closed while we move to a new location. We'll be back up and running soon, with the same great service. Please leave a message and a member of our team will return your call promptly.", 'General', 'General'],
        'appointment' => ['Appointment confirmation & reminders', 'Scheduling line', 'Thank you for calling {business}. To schedule a new appointment, press 1. To confirm, change or cancel an existing appointment, press 2. For all other questions, please stay on the line. As a reminder, please call at least 24 hours in advance if you need to reschedule.', 'General'],
        'welcome_sales' => ['New customer welcome / sales', 'Sales & new inquiries', "Thank you for calling {business}, and welcome! We're glad you reached out. If you're a new customer, please stay on the line and a member of our sales team will be happy to answer your questions and help you get started. {departments_menu}", 'General', 'General'],
        'support_queue' => ['Support queue / high call volume', 'Busy support periods', "Thank you for calling {business} support. We're currently experiencing a higher than normal call volume. Your call will be answered in the order it was received. Please stay on the line, or press 1 to leave a message and a support specialist will call you back. Our support hours are {hours}.", 'General'],
        'spanish_main' => ['Saludo principal (Español)', 'Spanish-speaking callers', 'Gracias por llamar a {business}. Su llamada es muy importante para nosotros. Si conoce la extensión de la persona con quien desea hablar, márquela ahora. De lo contrario, permanezca en la línea y uno de nuestros representantes le atenderá en breve. Nuestro horario de atención es {hours}.', 'General'],
        'bilingual' => ['Bilingual greeting (English / Español)', 'English & Spanish callers', 'Thank you for calling {business}. For English, press 1. Gracias por llamar a {business}. Para español, oprima el número 2.', 'General'],
        'directory' => ['Company directory / dial-by-name', 'Directory menu', "Thank you for calling {business}. If you know your party's extension, you may dial it at any time. To use our company directory, press 9, then enter the first three letters of the person's last name. {departments_menu} To speak with the operator, press 0.", 'General', 'General'],
        'seasonal' => ['Seasonal promotion', 'On-hold marketing', 'Thank you for holding. This season, {business} has special offers available for a limited time. Ask your representative about our current promotions when your call is answered, or visit our website for details. Thank you for your patience. We will be with you shortly.', 'General'],
        'after_emergency' => ['After-hours emergency line', 'Urgent after-hours calls', 'You have reached {business} after hours. Our office is open {hours}. If this is an urgent matter that cannot wait until the next business day, press 1 to be connected to our on-call staff. For all other calls, please leave a message and we will return your call the next business day. If this is a medical or life-threatening emergency, please hang up and dial 911.', 'General'],
        'operator' => ['IVR with operator option', 'Menu with live operator', 'Thank you for calling {business}. Please listen carefully, as our menu options have changed. {departments_menu} To speak with an operator, press 0 at any time. To repeat these options, press the star key.', 'General'],
        'fax_notice' => ['Fax line notice', 'Fax number callers', "You've reached the fax line for {business}. This number is for fax transmissions only. To speak with someone, please call our main number. If you are sending a fax, please press start now. Thank you.", 'General', 'General'],
        'thanksgiving' => ['Thanksgiving holiday', 'Thanksgiving closure', 'Happy Thanksgiving from all of us at {business}! Our office is closed for the Thanksgiving holiday and will reopen on the next business day. Our regular hours are {hours}. Please leave a message and we will return your call when we return. We are thankful for your business.', 'General'],
        'year_end' => ['Christmas & New Year holidays', 'Year-end closure', 'Season\'s greetings from everyone at {business}. Our office is closed for the holidays and will reopen after the New Year. Please leave your name, number and a brief message, and we will return your call as soon as we are back. Thank you for a wonderful year, and best wishes for the year ahead.', 'General'],
        // Medical / med spa clinic
        'medical_main' => ['Medical / med spa clinic – Main greeting', 'Main greeting', 'Thank you for calling {business}. If this is a medical emergency, please hang up and dial 911. {departments_menu} To schedule or change an appointment, please stay on the line and our front desk will assist you.', 'Medical / med spa clinic'],
        'medical_after' => ['Medical / med spa clinic – After-hours', 'After-hours', 'You have reached {business} after hours. Our office is open {hours}. If this is a medical emergency, please hang up and dial 911. Otherwise, please leave your name, date of birth and phone number, and our staff will return your call the next business day.', 'Medical / med spa clinic'],
        'medical_appt' => ['Medical / med spa clinic – Appointment line', 'Appointment line', 'Thank you for calling {business}. To book a new appointment or treatment consultation, press 1. To confirm, reschedule or cancel an existing appointment, press 2. Please arrive 15 minutes early for your visit, and call at least 24 hours ahead if you need to reschedule.', 'Medical / med spa clinic'],

        // Dental office
        'dental_office_main' => ['Dental office – Main greeting', 'Main greeting', 'Thank you for calling {business}, where we care about your smile. {departments_menu} For a dental emergency, please stay on the line and a team member will help you right away.', 'Dental office'],
        'dental_office_after' => ['Dental office – After-hours', 'After-hours', 'You\'ve reached {business}. Our office is currently closed. Our hours are {hours}. If you are experiencing a dental emergency, please leave a detailed message and we will return your call as soon as possible. For a life-threatening emergency, dial 911.', 'Dental office'],
        'dental_office_hold' => ['Dental office – On-hold message', 'On-hold message', 'Thank you for holding. At {business}, we recommend a checkup and cleaning every six months. Ask our front desk about scheduling your next visit. A team member will be with you shortly.', 'Dental office'],

        // Law firm
        'law_firm_main' => ['Law firm – Main greeting', 'Main greeting', 'Thank you for calling {business}. {departments_menu} If you are a new client seeking a consultation, please stay on the line and our intake team will assist you. All calls are kept confidential.', 'Law firm'],
        'law_firm_after' => ['Law firm – After-hours', 'After-hours', 'You have reached {business}. Our office is open {hours}. Please leave your name, phone number and a brief description of your matter, and a member of our team will return your call the next business day. Please do not leave confidential details in your message.', 'Law firm'],

        // Real estate
        'real_estate_main' => ['Real estate – Main greeting', 'Main greeting', 'Thank you for calling {business}. Whether you are buying, selling or renting, we are here to help. {departments_menu} To reach an agent directly, dial their extension at any time.', 'Real estate'],
        'real_estate_after' => ['Real estate – After-hours', 'After-hours', 'You\'ve reached {business}. Our office is open {hours}, but our agents are often available by appointment. Please leave your name, number and the property you are interested in, and an agent will call you back promptly.', 'Real estate'],

        // Restaurant
        'restaurant_main' => ['Restaurant – Main greeting', 'Main greeting', 'Thank you for calling {business}. Our hours are {hours}. To make a reservation, press 1. To place an order for pickup, press 2. For catering and private events, press 3. For all other questions, please stay on the line.', 'Restaurant'],
        'restaurant_busy' => ['Restaurant – Busy / peak hours', 'Busy / peak hours', 'Thank you for calling {business}. We are currently serving a full house and will be with you in just a moment. Please stay on the line, or visit our website to view our menu and place an order online.', 'Restaurant'],

        // Auto repair
        'auto_repair_main' => ['Auto repair – Main greeting', 'Main greeting', 'Thank you for calling {business}. {departments_menu} To schedule service or check on the status of your vehicle, please stay on the line for the next available service advisor.', 'Auto repair'],
        'auto_repair_after' => ['Auto repair – After-hours', 'After-hours', 'You\'ve reached {business}. Our shop is open {hours}. Please leave your name, number, and your vehicle\'s year, make and model, along with a short description of the issue, and we will call you back the next business day.', 'Auto repair'],

        // Salon / barber
        'salon_main' => ['Salon / barber – Main greeting', 'Main greeting', 'Thank you for calling {business}. To book, change or cancel an appointment, press 1. For our hours and location, press 2. To speak with a stylist, please stay on the line. Walk-ins are welcome when chairs are available.', 'Salon / barber'],
        'salon_after' => ['Salon / barber – After-hours', 'After-hours', 'You\'ve reached {business}. We\'re currently closed. Our hours are {hours}. Please leave your name, number and the service you\'d like to book, and we\'ll call you back to confirm your appointment.', 'Salon / barber'],

        // Insurance agency
        'insurance_agency_main' => ['Insurance agency – Main greeting', 'Main greeting', 'Thank you for calling {business}. {departments_menu} To report a claim, please stay on the line and an agent will assist you. If you need a quote, have your current policy information ready.', 'Insurance agency'],
        'insurance_agency_after' => ['Insurance agency – After-hours', 'After-hours', 'You have reached {business}. Our office is open {hours}. To report a claim after hours, please contact your insurance carrier\'s claims line listed on your policy. For all other matters, leave a message and an agent will return your call the next business day.', 'Insurance agency'],

        // Home services (plumbing / HVAC)
        'home_services_main' => ['Home services – Main greeting', 'Main greeting', 'Thank you for calling {business}. For emergency service, press 1. To schedule a repair, maintenance or estimate, press 2. To check on an existing appointment, press 3. For all other calls, please stay on the line.', 'Home services (plumbing / HVAC)'],
        'home_services_after' => ['Home services – After-hours emergency', 'After-hours', 'You\'ve reached {business} after hours. Our office is open {hours}. If you have an emergency such as a burst pipe, flooding, or no heat or cooling, press 1 to reach our on-call technician. If you smell gas, leave the building and call your gas company or 911. For non-urgent requests, please leave a message.', 'Home services (plumbing / HVAC)'],

        // Retail store
        'retail_store_main' => ['Retail store – Main greeting', 'Main greeting', 'Thank you for calling {business}. Our store hours are {hours}. To check product availability, press 1. For returns and exchanges, press 2. For our location and directions, press 3. To speak with a team member, please stay on the line.', 'Retail store'],
        'retail_store_hold' => ['Retail store – On-hold message', 'On-hold message', 'Thank you for holding. Did you know you can browse our latest arrivals on our website? Ask a team member about our current in-store offers. Someone from {business} will be with you shortly.', 'Retail store'],

        // Accounting / tax
        'accounting_main' => ['Accounting / tax – Main greeting', 'Main greeting', 'Thank you for calling {business}. {departments_menu} If you are a new client, please stay on the line to schedule a consultation. Please have your account or client number ready.', 'Accounting / tax'],
        'accounting_season' => ['Accounting / tax – Tax season', 'Tax season', 'Thank you for calling {business}. During tax season we are experiencing high call volume. To schedule an appointment, press 1. To check on the status of your return, press 2. To drop off documents, our office hours are {hours}. Please stay on the line and we will be with you as soon as possible.', 'Accounting / tax'],

        // Hotel / guesthouse
        'hotel_main' => ['Hotel / guesthouse – Main greeting', 'Main greeting', 'Thank you for calling {business}. To make a new reservation, press 1. To change or cancel a reservation, press 2. To reach the front desk, press 0. If you are calling for a guest, please stay on the line.', 'Hotel / guesthouse'],
        'hotel_hold' => ['Hotel / guesthouse – On-hold message', 'On-hold message', 'Thank you for holding. At {business}, we look forward to welcoming you. Ask about our room options, local attractions and guest amenities. A member of our front desk team will be with you shortly.', 'Hotel / guesthouse'],

        // Church / nonprofit
        'church_main' => ['Church / nonprofit – Main greeting', 'Main greeting', 'Thank you for calling {business}. {departments_menu} For information about services and events, please visit our website. Our office hours are {hours}. God bless, and thank you for calling.', 'Church / nonprofit'],
        'church_after' => ['Church / nonprofit – After-hours', 'After-hours', 'You\'ve reached {business}. Our office is currently closed. Our office hours are {hours}. If you have a pastoral emergency, please press 1. Otherwise, please leave your name, number and message, and we will return your call. Thank you for your support.', 'Church / nonprofit'],

        // Gym / fitness
        'gym_main' => ['Gym / fitness – Main greeting', 'Main greeting', 'Thank you for calling {business}. For membership information, press 1. For class schedules, press 2. For personal training, press 3. Our hours are {hours}. To speak with a team member, please stay on the line.', 'Gym / fitness'],
        'gym_hold' => ['Gym / fitness – On-hold message', 'On-hold message', 'Thanks for holding. At {business}, we\'re here to help you reach your fitness goals. Ask us about our group classes and personal training options. A team member will be with you shortly.', 'Gym / fitness'],
    ],
];
