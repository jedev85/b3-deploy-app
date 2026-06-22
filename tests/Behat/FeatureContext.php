<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;

final class FeatureContext implements Context
{
    private ?Crawler $crawler = null;

    public function __construct(private readonly KernelBrowser $client)
    {
    }

    #[When('I request :path')]
    public function requestPath(string $path): void
    {
        $this->crawler = $this->client->request('GET', $path);
    }

    #[When('I log in with :email and :password')]
    public function logInWithCredentials(string $email, string $password): void
    {
        $this->crawler = $this->client->request('GET', '/login');

        $form = $this->crawler->selectButton('Se connecter')->form([
            '_username' => $email,
            '_password' => $password,
        ]);

        $this->crawler = $this->client->submit($form);

        if ($this->client->getResponse()->isRedirect()) {
            $this->crawler = $this->client->followRedirect();
        }
    }

    #[Then('the response status code should be :expectedStatusCode')]
    public function responseStatusCodeShouldBe(int $expectedStatusCode): void
    {
        $actualStatusCode = $this->client->getResponse()->getStatusCode();

        if ($actualStatusCode !== $expectedStatusCode) {
            throw new \RuntimeException(sprintf(
                'Expected HTTP status code %d, got %d.',
                $expectedStatusCode,
                $actualStatusCode,
            ));
        }
    }

    #[Then('the page should contain :expectedText')]
    public function pageShouldContain(string $expectedText): void
    {
        $content = $this->client->getResponse()->getContent();

        if (false === $content || !str_contains($content, $expectedText)) {
            throw new \RuntimeException(sprintf('Expected page to contain "%s".', $expectedText));
        }
    }

    #[Then('the JSON field :field should equal :expectedValue')]
    public function jsonFieldShouldEqual(string $field, string $expectedValue): void
    {
        $content = $this->client->getResponse()->getContent();

        if (false === $content) {
            throw new \RuntimeException('Response body is empty.');
        }

        $data = json_decode($content, true, flags: \JSON_THROW_ON_ERROR);

        if (!is_array($data) || !array_key_exists($field, $data)) {
            throw new \RuntimeException(sprintf('JSON field "%s" was not found.', $field));
        }

        if ($data[$field] !== $expectedValue) {
            throw new \RuntimeException(sprintf(
                'Expected JSON field "%s" to equal "%s", got "%s".',
                $field,
                $expectedValue,
                is_scalar($data[$field]) ? (string) $data[$field] : get_debug_type($data[$field]),
            ));
        }
    }
}
