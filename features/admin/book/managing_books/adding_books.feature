@managing_books
Feature: Adding a new book
    In order to manage the library
    As an Administrator
    I want to add a new book

    @ui
    Scenario: Adding a new book
        When I want to create a new book
        And I name it "Carrie"
        And I specify its author as "Stephen King"
        And I add it
        Then the book "Carrie" should be added
        And the book "Carrie" should appear in the list
