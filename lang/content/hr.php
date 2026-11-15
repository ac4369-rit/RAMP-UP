<?php
// Croatian names for project content that comes from the database
// (resources, tasks, decisions, project info, timeline, change log, team rules).
// Key = the English text stored in the database, value = what is shown.
// Text that is not in this list is shown as it is stored.
return [
    // Resources
    'Design onboarding guide'  => 'Vodič za uvođenje u dizajn',
    'Figma file: Atlas'        => 'Figma datoteka: Atlas',
    'Old style guide (v1)'     => 'Stari stilski vodič (v1)',
    'Accessibility checklist'  => 'Kontrolni popis pristupačnosti',
    'Backend setup guide'      => 'Vodič za postavljanje backenda',
    'Code repository'          => 'Repozitorij koda',
    'Component list'           => 'Popis komponenti',

    // Tasks
    'Review the invoice page wireframe'   => 'Pregledaj wireframe stranice s računima',
    'Fix contrast on the login button'    => 'Popravi kontrast gumba za prijavu',
    'Build invoice download'              => 'Izradi preuzimanje računa',
    'Test order history on mobile'        => 'Testiraj povijest narudžbi na mobitelu',
    'Set up database'                     => 'Postavi bazu podataka',
    'Design the empty order history'      => 'Dizajniraj praznu povijest narudžbi',
    'Redesign the invoice download button' => 'Redizajniraj gumb za preuzimanje računa',

    // Decisions
    'Use PHP and MySQL for the backend'              => 'Za backend koristimo PHP i MySQL',
    'Invoices are generated as PDF only'             => 'Računi se izrađuju samo kao PDF',
    'Mobile-first layout for every screen'           => 'Svaki zaslon najprije za mobitel (mobile-first)',
    'Login with email and password, no social login' => 'Prijava e-poštom i lozinkom, bez društvenih mreža',

    // Project and sprint
    'Invoices' => 'Računi',
    'Customers can download every invoice as a PDF.' => 'Kupci mogu preuzeti svaki račun kao PDF.',
    'A web portal where customers can see their orders, download invoices and contact support in one place.' => 'Web-portal na kojem kupci na jednom mjestu vide svoje narudžbe, preuzimaju račune i javljaju se podršci.',
    'Existing customers of a mid-sized online shop and the support team that answers their questions.' => 'Postojeći kupci srednje velike web-trgovine i tim podrške koji odgovara na njihova pitanja.',
    'Login and order history are finished. The invoice page is in progress and support chat starts next sprint.' => 'Prijava i povijest narudžbi su gotove. Stranica s računima je u izradi, a chat podrške počinje u sljedećem sprintu.',

    // Milestones
    'Login and accounts' => 'Prijava i korisnički računi',
    'Order history' => 'Povijest narudžbi',
    'Invoice page' => 'Stranica s računima',
    'Support chat' => 'Chat podrške',
    'Public launch' => 'Javno pokretanje',

    // Change log
    'Invoices will be PDF only' => 'Računi će biti samo u PDF-u',
    'HTML invoices were dropped to save time.' => 'HTML računi su izbačeni kako bi se uštedjelo vrijeme.',
    'Jordan left the team' => 'Jordan je napustio tim',
    'Jordan moved to another project. Alex takes over UX.' => 'Jordan je prešao na drugi projekt. Alex preuzima UX.',
    'Support chat moved to next sprint' => 'Chat podrške premješten je u sljedeći sprint',
    'The invoice page needed more time.' => 'Stranica s računima trebala je više vremena.',
    'Order history released to testers' => 'Povijest narudžbi puštena je testerima',
    'First 20 customers can now see their orders.' => 'Prvih 20 kupaca sada vidi svoje narudžbe.',

    // Team rules
    'Meetings: Tuesday 18:00 on Discord' => 'Sastanci: utorkom u 18:00 na Discordu',
    'Reply within 24 hours' => 'Odgovori unutar 24 sata',
    'Decisions go in the #decisions channel' => 'Odluke idu u kanal #decisions',
    'Make file names easy to find' => 'Daj datotekama nazive koje je lako pronaći',
];
