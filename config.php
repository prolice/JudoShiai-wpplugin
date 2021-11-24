<?php
/* Date of the Tournament.
This is necessary information to calculate the contestants age and determine
which category they will compete in. Further this information is used to
automatically disable registration after the Tournament began */
$dateOfTournament='01.10.2022';
/* Registration closing date and how to handle late registrations
Typically you close registration a few days before the Turnament, but that does
not keep some people from registering late.
You have the following options to handle late registrations:
'reject' will not allow any further registrations after closing.
'warn' will prominently show a warning banner and mark late registrations as such in the database.
'allow' will simply accept late registrations. */
$registrationClosingDate='01.09.2022';
$lateRegistrationHandling='reject';
/* The minimum and maximum yearOfBirth for competitors.
Input will be validated against these boundaries and hopefully nobody will try
to enter a way to young or to old competitor. */
$minYearOfBirth=1980;
$maxYearOfBirth=2016;
/* Filenames of different config files.
The registration website reads information such as Tournament name and
Description as well as category definitions from a JudoShiai file. For the clubs
options the clubs.txt file as utilised by JudoShiais autocomplete feature is
used. See JudoShiai docu for more information on clubs.txt. dataCsv is the name
of the file where the registrations shall be stored in csv format. */
$judoShiaiTemplateFile='RIGW_Open.shi';
$clubsTxt='noclubs.txt';
$dataCsv='data.csv';
/* For internationalisation you can set the default locale to use when nothing
else is requested by the user. */
$defaultLocale="fr_FR";
/* If you want to communicate with the registering coaches you need their email
address. Set forceRegistration to true if you want to enforce the coaches to
create a Coach Id with their email address. */
$forceRegistration=true;
/* For official tournaments you only invite clubs within the league. So you can
list all clubs in the ClubsTxt and only those clubs may register Competitors.
For open tournaments you want the invitation to spread wide and do not know
in advance from where Fighters might be registert. In this case you want to
allow people to enter custom club names. */
$allowCustomClub=true;
/* Disable Registration and show permanent error Message.
Sometimes you want to manually disable registration for whatever reason.
e.g the Tournament got canceled, you see to much spam and want contestents to
register by other means. In this case set disabled to true and put whatever you
want potential visitors to know in the disabledErrorMessage. */
$disabled=false;
$disabledErrorMessage='The Tournament got canceled due to way too little'
        . 'prospective competitors';
/* In case you use the collected data for other purposes than only what is
necessary to execute the tournament and verify payment for the tournament
consent of the contestants to this further data usage is necessary according to
the EU General Data Protection Regulation (GDPR), you can enable a simple
checkbox asking for this consent by setting showLegalConsentCheckbox to true.
It is recommended that you also edit customLegalConsentText to inform users
about the intended further data usage. e.g you can ask for consent to publish
the fotos made on the event or for publishing the names of winners online.
It is forbidden by GPDR to make this consent obligatory. */
$showInputLegalConsent=false;
$customLegalConsentText='';
/* For a tournament it is not crucial to know weights of fighters in advance.
For some official turnaments however a registration with weight might be
required. For turnaments not using official weight at all the precense of this
input field might confuse users.
inputWeight can take the following values:
'none' will not show the weight form field at all
'optional' will show the form field, which is the default
'required' will show the form field and makes it mandatory. */
$inputWeight='required';
/* There are two alternative ways to get the information which age category a
competitor will start in. You can have people directly enter the age category of
theire competitors or you ask for the year of birth and calculate the age
category from this. The first allows users to intentionaly put a junger talented
fighter into the higher age category. The later is helpfull if fighters might be
confused about which age category they should register for; e.g. when your
tournament has atypical age categories.
input Age may take the following values:
'yearOfBirth' The age category will be calculated from the entered year of birth
'category' The age category can directly be entered */
$inputAge='yearOfBirth';
/* Alternative registration email address.
For whatever reason some people might not be able/comforable with using the
registration page. There is a troubleshooting link, wich will show this email
address and instructions how to register via email. */
$emailAlternativeRegistration='tournoi@rigw.be';
?>
