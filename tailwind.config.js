/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.html", "./pages/**/*.html"],
  theme: {
    extend: {
      fontFamily: { sans: ["Inter", "sans-serif"] },
      colors: {
        primary: { 50: "#EEF6FB", 100: "#D4E8F5", 200: "#AACEEA", 300: "#7BACC4", 400: "#50A2D5", 500: "#2E86C1", 600: "#2474AB", 700: "#1B5F8A", 800: "#144A6A", 900: "#0D3450" },
        accent: { 100: "#FDE8D0", 200: "#FBCFA0", 400: "#F5A050", 500: "#E8832A", 600: "#CC6F1E" }
      }
    }
  },
  plugins: []
};
