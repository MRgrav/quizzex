/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./app/Livewire/**/*.php",
    ],
    darkMode: 'none',
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: 'hsl(217 91% 35%)',
                    dark: 'hsl(217 91% 25%)',
                },
            },
            backgroundImage: {
                'primary-gradient': 'linear-gradient(135deg, hsl(217 91% 35%) 0%, hsl(217 91% 25%) 100%)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
