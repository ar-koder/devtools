// homepage.spec.js created with Cypress
//
// Start writing your Cypress tests below!
// If you're unfamiliar with how Cypress works,
// check out the link below and learn how to write your first test:
// https://on.cypress.io/writing-first-test
/* ==== Test Created with Cypress Studio ==== */
it('RGPD Opt-In', function() {
  /* ==== Generated with Cypress Studio ==== */
  cy.visit('/')
  /* ==== End Cypress Studio ==== */
  /* ==== Generated with Cypress Studio ==== */
  cy.get('#\\30 1d20bff-b18c-44c2-8a2c-1a16dd0ea668').should('be.visible');
  cy.get('#ppms_cm_agree-to-all').click();
  cy.get('#\\30 1d20bff-b18c-44c2-8a2c-1a16dd0ea668').should('not.exist');
  /* ==== End Cypress Studio ==== */
});

it('Go to EasyAdmin', function() {
  /* ==== Generated with Cypress Studio ==== */
  cy.visit('/')
  cy.get('.sidebar__menu > .navigation-menu > ul > :nth-child(15) > .item__link')
    .should('have.text', 'EasyAdmin')
    .should('have.attr', 'href').and('include', '/admin')
    .then((href) => {
      cy.visit(href)
    });
  /* ==== End Cypress Studio ==== */
});
