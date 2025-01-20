@managing_books
Feature: Editing books
    In order to change information about a book
    As an Administrator
    I want to be able to edit the book

    Background:
        Given there is a book "The Shining"

    @ui
    Scenario: Renaming a book
        When I want to edit this book
        And I rename it to "Carrie"
        And I save my changes
        Then this book title should be "Carrie"
