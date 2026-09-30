main.css = design.part.css (the Sensula design stylesheet) + wp.part.css (WordPress/WooCommerce screens).
After editing either part:  cat src/design.part.css src/wp.part.css > main.css  and re-minify to main.min.css
(e.g. npx csso-cli main.css -o main.min.css). Or just edit main.css and delete main.min.css.
