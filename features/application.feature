Feature: Application smoke tests

    Scenario: Health check exposes database status
        When I request "/health"
        Then the response status code should be 200
        And the JSON field "database" should equal "ok"

    Scenario: Admin can log in
        When I log in with "admin@deploylab.test" and "password"
        Then the response status code should be 200
        And the page should contain "Dashboard"
        And the page should contain "admin@deploylab.test"
