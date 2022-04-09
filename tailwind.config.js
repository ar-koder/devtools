const plugin = require('tailwindcss/plugin')

module.exports = {
  content: [
    'templates/**/*.html.twig',
    'assets/js/**/*.js'
  ],
  darkMode: 'media',
  theme: {
    extend: {
      boxShadow: {
        gitter:
          'rgba(136, 172, 243, 0.25) 0px 10px 30px, rgba(0, 0, 0, 0.03) 0px 1px 1px, rgba(0, 51, 167, 0.1) 0px 10px 20px;'
      },
      fontFamily: {
        mono: ['"IBM Plex Mono"', 'ui-monospace'], // Ensure fonts with spaces have " " surrounding it.
        sans: [
          '"IBM Plex Sans"',
          'system-ui',
          '-apple-system',
          'BlinkMacSystemFont',
          '"Segoe UI"',
          'Roboto',
          '"Helvetica Neue"',
          'Arial',
          '"Noto Sans"',
          'sans-serif',
          '"Apple Color Emoji"',
          '"Segoe UI Emoji"',
          '"Segoe UI Symbol"'
        ] // Ensure fonts with spaces have " " surrounding it.
      },
      fontSize: {
        xs: '0.75rem',
        sm: '0.875rem',
        base: '1rem',
        lg: '1.125rem',
        xl: '1.25rem',
        '2xl': '1.5rem',
        '3xl': '1.875rem',
        '4xl': '2.25rem',
        '5xl': '3rem',
        '6xl': '4rem',
        '7xl': '5rem',
        '8xl': '6rem',
        '9xl': '7rem',
        '10xl': '8rem',
        '11xl': '9rem',
        '12xl': '10rem',
        '13xl': '11rem',
        '14xl': '12rem',
        '15xl': '13rem',
        '16xl': '14rem',
        '17xl': '15rem',
        '18xl': '16rem',
        '19xl': '17rem',
        '20xl': '18rem'
      },
      colors: {
        // whites
        haze: '#f6f8fa',
        alabaster: '#f9fafb',
        ghost: '#f9fafe',
        gallery: '#f0f0f0',
        smoke: '#f0f0f0',
        mercury: '#e4e5e5',
        // dark
        metal: '#c5c7d3',
        santa: '#a0a1b2',
        manatee: '#9596a9',
        waterloo: '#7f8198',
        storm: '#676d89',
        comet: '#5c617a',
        bay: '#51566c',
        river: '#464a5d',
        // github scheme
        gun: '#2d333b',
        tuna: '#22272e',
        cinder: '#1c2128',
        pearl: '#1e2028',
        // system
        gitter: '#e4eeff',
        icy: '#d5ddfe',
        periwinkly: '#bfccfd',
        pastel: '#aabbfc',
        widowmaker: '#95aafc',
        periblue: '#8098fb',
        punch: '#6b87fa',
        oyster: '#5576f9',
        mana: '#4065f9',
        ultramarine: '#445cff',

        // highlight
        flamingo: '#ff99ac',
        mona: '#ff9a8b',
        brink: '#fe6b88',
        magenta: '#ef6bad',
        fuchsia: '#eb4899',
        rose: '#cf1872',

        // system
        fadedred: '#fb5a8c',
        carnation: '#f84d6b',
        coral: '#ee2a1b',
        herbs: '#00e0cc',
        shamrock: '#00d0d4',
        jade: '#06b0e4',
        baby: '#9cbff9',
        nation: '#5b8def',
        grandis: '#fccc74',
        pumpikin: '#fdad41',
        carrot: '#ff8800',
        cryon: '#ff8975',
        energy: '#ff8379',
        tangerine: '#ff6b8b',
        glass: '#a8eff2',
        fluor: '#72e0e7',
        ice: '#00cfde',
        plum: '#dda5e8',
        lilac: '#ab5dd9',
        heart: '#6600cc'
      },
      typography: {
        DEFAULT: {
          css: {
            code: {
              color: '#7c74da'
            },
            pre: {
              backgroundColor: '#1c2128',
              borderWidth: '1px',
              borderColor: '#2d333b'
            }
          }
        }
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/aspect-ratio'),
    plugin(function ({ addVariant, e, postcss }) {
      addVariant('firefox', ({ container, separator }) => {
        const isFirefoxRule = postcss.atRule({
          name: '-moz-document',
          params: 'url-prefix()'
        })
        isFirefoxRule.append(container.nodes)
        container.append(isFirefoxRule)
        isFirefoxRule.walkRules((rule) => {
          rule.selector = `.${e(
            `firefox${separator}${rule.selector.slice(1)}`
          )}`
        })
      })
    }),
    require('tailwind-css-extensions')({
      base: ['./assets/tailwind/base/**/*.{css,pcss}'], // Glob paths to your bases
      utilities: ['./assets/tailwind/utilities/**/*.{css,pcss}'], // Glob paths to your utilities
      components: ['./assets/tailwind/components/**/*.{css,pcss}'] // Glob paths to your components
    })
  ]
}
