<div class="flex flex-col gap-y-2 sm:flex-row sm:items-center sm:justify-between p-4 bg-primary-50 dark:bg-primary-950/20 rounded-lg mb-4">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-primary-600 dark:text-primary-400">
            Gestión de Embarcaciones
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Total de embarcaciones registradas: <span class="font-medium">{{ $totalVessels }}</span>
        </p>
    </div>
    <div class="flex items-center gap-x-4">
        <div class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <span>Utilice los filtros para encontrar embarcaciones específicas</span>
        </div>
    </div>
</div>
