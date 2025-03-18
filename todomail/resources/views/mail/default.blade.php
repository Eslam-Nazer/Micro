<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mail</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <section class="max-w-2xl px-6 py-8 mx-auto bg-white dark:bg-gray-900">
        <header>
            <h1>
                Todo List Service
            </h1>
        </header>

        <main class="mt-8">
            <h2 class="text-gray-700 dark:text-gray-200">Hi {{ $name }},</h2>

            <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
                This mail from Todo Lsit Service:
                <span class="font-semibold">"{{ $todo }}"</span>.
            </p>

            <button class="px-6 py-2 mt-4 text-sm font-medium tracking-wider text-white capitalize transition-colors duration-300 transform bg-blue-600 rounded-lg hover:bg-blue-500 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-80 cursor-pointer">
                Go to todo list to show all
            </button>

            <p class="mt-8 text-gray-600 dark:text-gray-300">
                Thanks,<br>
                {{ env('MAIL_FROM_ADDRESS') }}
            </p>
        </main>

    </section>

</body>

</html>