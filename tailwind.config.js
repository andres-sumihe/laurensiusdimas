import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', 'Manrope', 'Inter', 'system-ui', 'sans-serif'],
                display: ['Horizon', 'Bebas Neue', 'Oswald', 'sans-serif'],
                body: ['Montserrat', 'sans-serif'],
                mono: ['Space Mono', 'monospace'],
                horizon: ['Horizon', 'sans-serif'],
                montserrat: ['Montserrat', 'sans-serif'],
                spacemono: ['Space Mono', 'monospace'],
            },
            animation: {
                'fade-in': 'fadeIn 1s ease-out',
                'fade-in-delay': 'fadeIn 1s ease-out 0.3s both',
                'fade-in-delay-2': 'fadeIn 1s ease-out 0.6s both',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },
}
