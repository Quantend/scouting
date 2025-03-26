<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-2">
        <div class="relative aspect-video overflow-hidden rounded-xl bg-gray-100 shadow-md dark:bg-gray-800">
            <div class="absolute inset-0 flex justify-center items-center flex-col">
                @if($isAdmin)
                    <div>
                        <a href="{{ route('hours') }}"
                           class="bg-blue-500 p-2 rounded hover:bg-blue-600 transition duration-300 text-white">
                            Klik hier om uren te registreren
                        </a>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('charts') }}"
                           class="bg-blue-500 p-2 rounded hover:bg-blue-600 transition duration-300 text-white">
                            Klik hier om uren in te zien
                        </a>
                    </div>
                @else
                    <div class="p-10">
                        <p>
                            Het lijkt erop dat je nog geen admin-rechten hebt. Neem contact op met Dylan om deze te krijgen. <br>
                            Vergeet niet je e-mailadres te vermelden (het e-mailadres dat je hebt gebruikt voor je  account).
                        </p>
                    </div>
                @endif
            </div>
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl bg-gray-100 shadow-md dark:bg-gray-800">
            <div class="absolute inset-0 flex justify-center items-center">
                <p class="text-center">Totaal aantal gewerkte uren: {{ number_format($totalHours, 2)  }} <br>
                    Totaal geld gemaakt: {{ $totalMoney }}€ <br>
                    MVP: {{ $mvp->name ?? "no names yet" }} met {{ number_format($totalHoursMvp, 2) }} uren <br>
                    LVP: {{ $lvp->name ?? "no names yet" }} met {{ number_format($totalHoursLvp, 2) }} uren
                </p>
            </div>
        </div>
    </div>
    <div
        class="relative flex-1 overflow-scroll-y rounded-xl bg-gray-100 shadow-md dark:bg-gray-800 flex justify-center items-center">
        <p class="text-center text-gray-800 dark:text-white text-lg leading-relaxed">
            Hallo, Mijn naam is Dylan, en ik ben de maker van deze web app.<br>
            Ik heb deze app gemaakt omdat dropbox en excel vreselijk zijn.<br>
            Steeds zit ik in het verkeerde bestand of werkt excel om een of andere reden niet of kan ik het überhaupt
            niet vinden.<br>
            Dus heb ik deze app voor mezelf gemaakt om van dat gedoe af te komen.<br><br>
            Met vriendelijke groet,<br>
            Dylan
        </p>
    </div>
</div>
