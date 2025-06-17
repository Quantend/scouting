<!DOCTYPE html>
<html>
<head>
    <title>
        @switch($context)
            @case('manual')
                Handmatige SQLite Backup
                @break
            @case('cron')
                Wekelijkse Automatische SQLite Backup
                @break
            @case('reset')
                Backup vóór App Reset
                @break
            @case('clearlogs')
                Backup vóór Log-Opschoning
                @break
            @default
                SQLite Database Backup
        @endswitch
    </title>
</head>
<body>
<h1>Hallo Dylan,</h1>

@switch($context)
    @case('manual')
        <p>Dit is een handmatig verstuurde databasebackup van je scouting klussen applicatie.</p>
        @break

    @case('cron')
        <p>Dit is de automatische wekelijkse backup van je scouting klussen applicatie.</p>
        <p>Als je deze mail niet wekelijks ontvangt, is er iets mis met de cron job.</p>
        @break

    @case('reset')
        <p>Deze backup is automatisch verstuurd vlak vóór een volledige reset van de applicatie.</p>
        <p>Als dit niet de bedoeling is check de logs.</p>
        @break

    @case('clearlogs')
        <p>Deze databasebackup werd gegenereerd vlak vóór het opschonen van de logbestanden.</p>
        <p>Als dit niet de bedoeling is check de logs.</p>
        @break

    @default
        <p>Dit is een backup van de database.</p>
@endswitch

</body>
</html>
