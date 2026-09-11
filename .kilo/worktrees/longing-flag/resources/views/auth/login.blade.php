<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin - E-Commerce</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-lg p-8">

            <div class="text-center mb-8">

                <h1 class="text-2xl font-bold text-gray-900">
                    Admin E-Commerce
                </h1>

                <p class="text-gray-500 mt-2">
                    Silakan masuk ke akun admin
                </p>

            </div>


            @if ($errors->any())

                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4">

                    <p class="text-sm text-red-600">
                        {{ $errors->first() }}
                    </p>

                </div>

            @endif


            <form action="{{ route('login.process') }}" method="POST">

                @csrf


                <div class="mb-5">

                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@ecommerce.test"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:border-transparent"
                    >

                </div>


                <div class="mb-6">

                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               focus:border-transparent"
                    >

                </div>


                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700
                           text-white font-semibold rounded-lg
                           transition duration-200"
                >
                    Masuk
                </button>

            </form>

        </div>

    </div>

</body>

</html>