module.exports = {
  bail: true,
  verbose: !process.env.CI,
  testEnvironment: 'jsdom',
  transform: {
    '^.+\\.(j|t)sx?$': 'babel-jest'
  },
  moduleNameMapper: {
    '^@app/(.*)$': '<rootDir>/assets/app/$1'
  },
  testMatch: ['<rootDir>/tests/jest/**/*.test.(js|jsx|ts|tsx)'],
  watchPathIgnorePatterns: ['<rootDir>/node_modules/', '<rootDir>/vendor/']
}
