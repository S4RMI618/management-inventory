import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
        "./resources/css/**/*.css",
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: "#1EA7FD", // azul neón
                    dark: "#222E44", // azul oscuro
                    light: "#30D5C8", // turquesa
                    soft: "#485273", // azul-gris
                    bg: "#181F32", // fondo app
                },
                accent: {
                    DEFAULT: "#30D5C8", // turquesa
                    bright: "#00b4d8", // celeste brillante
                },
            },
            borderRadius: {
                xl: "1rem",
                "2xl": "1.5rem",
            },
            boxShadow: {
                xl: "0 8px 40px 0 rgba(30, 167, 253, 0.30)",
            },
        },
    },

    plugins: [forms],
};
