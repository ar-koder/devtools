module.exports = {
  '*.php': ['vendor/bin/phpinsights -n --ansi --format=github-action --fix'],
  '*.{js,jsx,ts,tsx,vue}': ['npm run lint:js'],
  '*.{pcss,css}': ['npm run lint:css'],
  '*.{json,yml,yaml,md}': ['prettier --write']
}
