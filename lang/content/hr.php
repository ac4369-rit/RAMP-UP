<?php
// Croatian text for project content that comes from the database
// (titles, descriptions, notes, messages, glossary, decisions, team info).
// Key = the English text exactly as stored in the database, value = what is shown.
// Text that is not in this list is shown as it is stored (for example new posts people write).
// Used by tc() in src/helpers.php.
return [

    // Resources
    'Design onboarding guide' => 'Vodič za uvođenje u dizajn',
    'Figma file: Atlas' => 'Figma datoteka: Atlas',
    'Old style guide (v1)' => 'Stari stilski vodič (v1)',
    'Accessibility checklist' => 'Kontrolni popis pristupačnosti',
    'Backend setup guide' => 'Vodič za postavljanje backenda',
    'Code repository' => 'Repozitorij koda',
    'Component list' => 'Popis komponenti',

    // Tasks: titles
    'Review the invoice page wireframe' => 'Pregledaj wireframe stranice s računima',
    'Fix contrast on the login button' => 'Popravi kontrast gumba za prijavu',
    'Build invoice download' => 'Izradi preuzimanje računa',
    'Test order history on mobile' => 'Testiraj povijest narudžbi na mobitelu',
    'Set up database' => 'Postavi bazu podataka',
    'Design the empty order history' => 'Dizajniraj praznu povijest narudžbi',
    'Redesign the invoice download button' => 'Redizajniraj gumb za preuzimanje računa',

    // Tasks: descriptions
    'Leave comments on the invoice page design in Figma.' => 'Ostavi komentare na dizajn stranice s računima u Figmi.',
    'The button text fails the 4.5:1 contrast check.' => 'Tekst na gumbu ne prolazi provjeru kontrasta 4.5:1.',
    'Customers can download a PDF of each invoice.' => 'Kupci mogu preuzeti PDF svakog računa.',
    'Check the page at 320px width and 200% zoom.' => 'Provjeri stranicu na širini od 320 px i uz povećanje od 200 %.',
    'Create tables and demo data.' => 'Izradi tablice i demo podatke.',
    'Customers with no orders see a blank page. Design a friendly empty state.' => 'Kupci bez narudžbi vide praznu stranicu. Dizajniraj prijateljsko prazno stanje.',
    'Make the download button match the new button style.' => 'Uskladi gumb za preuzimanje s novim stilom gumba.',

    // Tasks: notes, progress and feedback
    "Previous work: I planned the page structure and marked the open questions in Figma (page \"Invoice\", red comments).\nWhat you need to do: go through the wireframe, answer my comments and add your own.\nImportant: keep it consistent with the order history page. Maya decides on anything about the PDF layout." => "Dosadašnji rad: isplanirao sam strukturu stranice i označio otvorena pitanja u Figmi (stranica \"Invoice\", crveni komentari).\nŠto trebaš napraviti: prođi kroz wireframe, odgovori na moje komentare i dodaj svoje.\nVažno: drži se istog stila kao stranica s poviješću narudžbi. O svemu vezanom uz izgled PDF-a odlučuje Maya.",
    'PDF generation works, download button still missing.' => 'Izrada PDF-a radi, gumb za preuzimanje još nedostaje.',
    'Ready for review, tested on 3 phones.' => 'Spremno za pregled, testirano na 3 mobitela.',
    'New button style applied on the invoice list and invoice page.' => 'Novi stil gumba primijenjen je na popis računa i stranicu s računom.',
    'Please check the spacing around the button and use the same size as the buttons on the order history page.' => 'Provjeri razmak oko gumba i koristi istu veličinu kao gumbi na stranici s poviješću narudžbi.',

    // Decisions: titles
    'Use PHP and MySQL for the backend' => 'Za backend koristimo PHP i MySQL',
    'Invoices are generated as PDF only' => 'Računi se izrađuju samo kao PDF',
    'Mobile-first layout for every screen' => 'Svaki zaslon najprije za mobitel (mobile-first)',
    'Login with email and password, no social login' => 'Prijava e-poštom i lozinkom, bez društvenih mreža',

    // Decisions: why and what changes
    'The whole team already knows PHP and MySQL, and the school server supports them.' => 'Cijeli tim već zna PHP i MySQL, a školski poslužitelj ih podržava.',
    'No new framework to learn. We use Composer for the few libraries we need.' => 'Ne treba učiti novi framework. Za ono malo biblioteka koje trebamo koristimo Composer.',
    'Building both HTML and PDF invoices would take two extra weeks.' => 'Izrada i HTML i PDF računa trajala bi dva tjedna dulje.',
    'Customers download a PDF. The design team must create one PDF template.' => 'Kupci preuzimaju PDF. Dizajnerski tim mora izraditi jedan PDF predložak.',
    'Most customers open the portal on their phone.' => 'Većina kupaca otvara portal na mobitelu.',
    'All designs start at 360px width. Desktop is an enlarged version.' => 'Svi dizajni počinju na širini od 360 px. Verzija za računalo je uvećana verzija.',
    'Social login needs extra privacy checks the client does not want yet.' => 'Prijava preko društvenih mreža traži dodatne provjere privatnosti koje klijent još ne želi.',
    'Only email and password. Passwords are stored as hashes.' => 'Samo e-pošta i lozinka. Lozinke se spremaju kao hashevi.',

    // Decisions: options that were compared
    'Everyone knows it, runs on the school server.' => 'Svi ga znaju, radi na školskom poslužitelju.',
    'Modern, but only one person knows it.' => 'Moderno, ali zna ga samo jedna osoba.',
    'HTML and PDF' => 'HTML i PDF',
    'PDF only' => 'Samo PDF',
    'One template, faster to build.' => 'Jedan predložak, brže za izradu.',
    'Two templates, two extra weeks.' => 'Dva predloška, dva tjedna više.',

    // Glossary
    'Handoff' => 'Primopredaja',
    'Passing finished design work to developers with all details.' => 'Predaja gotovog dizajna programerima sa svim detaljima.',
    'Figma, task board' => 'Figma, ploča sa zadacima',
    'Invoice' => 'Račun',
    'The official PDF bill for one order.' => 'Službeni PDF račun za jednu narudžbu.',
    'Invoice page' => 'Stranica s računima',
    'Order' => 'Narudžba',
    'Everything a customer bought in one checkout.' => 'Sve što je kupac kupio u jednoj kupnji.',
    'Order history page' => 'Stranica s poviješću narudžbi',
    'Sprint' => 'Sprint',
    'A two-to-three-week block of work with a fixed goal.' => 'Razdoblje rada od dva do tri tjedna s određenim ciljem.',
    'Planning, Where are we now' => 'Planiranje, Gdje smo sada',
    'Ticket' => 'Tiket',
    'A support request a customer sends to the support team.' => 'Zahtjev za pomoć koji kupac šalje timu podrške.',
    'Support chat' => 'Chat podrške',

    // Questions and answers
    'Do customers need to download old invoices from before the portal existed?' => 'Trebaju li kupci moći preuzeti stare račune iz vremena prije portala?',
    'Only invoices from the last 12 months. Older ones stay in the old system.' => 'Samo račune iz posljednjih 12 mjeseci. Stariji ostaju u starom sustavu.',
    'Which PDF template should the invoice design follow?' => 'Koji PDF predložak treba pratiti dizajn računa?',
    'Use the template in the Figma file, page "Invoice PDF".' => 'Koristi predložak iz Figma datoteke, stranica "Invoice PDF".',
    'Where can I find the latest logo files?' => 'Gdje mogu pronaći najnovije datoteke logotipa?',

    // Say hello wall
    'Welcome to the team, Alex! Leo will be your buddy for the first weeks.' => 'Dobro došao u tim, Alex! Leo će ti biti buddy prvih nekoliko tjedana.',
    'Hi everyone, I am Sam. I build the screens customers see, so ping me about anything visual!' => 'Bok svima, ja sam Sam. Izrađujem zaslone koje kupci vide, pa mi se javite za sve što je vizualno!',

    // Job titles
    'Project Lead' => 'Voditeljica projekta',
    'Senior Developer' => 'Viši programer',
    'Frontend Developer' => 'Frontend programer',
    'Backend Developer' => 'Backend programer',
    'Junior Developer' => 'Mlađi programer',
    'UX Designer' => 'UX dizajner',

    // Skills
    'Accessibility' => 'Pristupačnost',
    'Code Review' => 'Pregled koda',
    'UX Design' => 'UX dizajn',
    'Visual Design' => 'Vizualni dizajn',

    // Project 1: Atlas
    'Invoices' => 'Računi',
    'Customers can download every invoice as a PDF.' => 'Kupci mogu preuzeti svaki račun kao PDF.',
    'A web portal where customers can see their orders, download invoices and contact support in one place.' => 'Web-portal na kojem kupci na jednom mjestu vide svoje narudžbe, preuzimaju račune i javljaju se podršci.',
    'Existing customers of a mid-sized online shop and the support team that answers their questions.' => 'Postojeći kupci srednje velike web-trgovine i tim podrške koji odgovara na njihova pitanja.',
    'Login and order history are finished. The invoice page is in progress and support chat starts next sprint.' => 'Prijava i povijest narudžbi su gotove. Stranica s računima je u izradi, a chat podrške počinje u sljedećem sprintu.',

    // Project 2: Library Booking System
    'Discovery' => 'Istraživanje',
    'Agree on the booking flow with the library staff.' => 'Dogovoriti tijek rezervacije s osobljem knjižnice.',
    'Online system for booking study rooms at the university library.' => 'Mrežni sustav za rezervaciju soba za učenje u sveučilišnoj knjižnici.',
    'Students who need a quiet room and the library staff who manage the rooms.' => 'Studenti kojima treba mirna soba i osoblje knjižnice koje upravlja sobama.',
    'Planning phase: wireframes are being reviewed.' => 'Faza planiranja: wireframeovi se pregledavaju.',

    // Milestones
    'Login and accounts' => 'Prijava i korisnički računi',
    'Order history' => 'Povijest narudžbi',
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
