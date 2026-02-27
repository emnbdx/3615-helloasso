<?php

namespace App;

use League\OAuth2\Client\Provider\GenericProvider;
use OpenAPI\Client\Configuration;
use OpenAPI\Client\Api\AnnuaireApi;
use OpenAPI\Client\Api\FormulairesApi;
use OpenAPI\Client\Model\HelloAssoApiV5CommonModelsDirectoryListFormsRequest;
use GuzzleHttp\Client;

class HelloAssoClient
{
    private const TOKEN_FILE = __DIR__ . '/../.token_cache.json';

    private GenericProvider $provider;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim($_ENV['API_URL'], '/');

        $this->provider = new GenericProvider([
            'clientId' => $_ENV['CLIENT_ID'],
            'clientSecret' => $_ENV['CLIENT_SECRET'],
            'urlAccessToken' => $_ENV['API_AUTH_URL'],
            'urlAuthorize' => '',
            'urlResourceOwnerDetails' => ''
        ]);
    }

    private function loadTokenCache(): ?array
    {
        if (!file_exists(self::TOKEN_FILE)) {
            return null;
        }
        $data = json_decode(file_get_contents(self::TOKEN_FILE), true);
        if (!$data || !isset($data['access_token'], $data['expires_at'])) {
            return null;
        }
        return $data;
    }

    private function saveTokenCache(string $accessToken, int $expiresAt, ?string $refreshToken): void
    {
        $data = [
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
            'refresh_token' => $refreshToken
        ];
        file_put_contents(self::TOKEN_FILE, json_encode($data));
    }

    private function getAccessToken(): string
    {
        $cache = $this->loadTokenCache();

        if ($cache && time() < $cache['expires_at'] - 60) {
            return $cache['access_token'];
        }

        if ($cache && !empty($cache['refresh_token'])) {
            try {
                $token = $this->provider->getAccessToken('refresh_token', [
                    'refresh_token' => $cache['refresh_token']
                ]);
                $this->saveTokenCache(
                    $token->getToken(),
                    $token->getExpires(),
                    $token->getRefreshToken() ?? $cache['refresh_token']
                );
                return $token->getToken();
            } catch (\Exception $e) {
            }
        }

        $token = $this->provider->getAccessToken('client_credentials');
        $this->saveTokenCache(
            $token->getToken(),
            $token->getExpires(),
            $token->getRefreshToken()
        );
        return $token->getToken();
    }

    private function getConfig(): Configuration
    {
        return Configuration::getDefaultConfiguration()
            ->setAccessToken($this->getAccessToken())
            ->setHost($this->apiUrl);
    }

    public function searchForms(string $query, int $pageSize = 20): array
    {
        $api = new AnnuaireApi(new Client(), $this->getConfig());

        $request = new HelloAssoApiV5CommonModelsDirectoryListFormsRequest([
            'form_name' => $query,
            'form_types' => ['Event'],
        ]);

        try {
            $result = $api->directoryFormsPost($pageSize, null, $request);
            $items = $result->getData() ?? [];

            $forms = [];
            foreach ($items as $item) {
                try {
                    $record = $item->getRecord();
                    if (!$record) continue;
                    $forms[] = [
                        'formType' => $record->getFormType() ?? '',
                        'formSlug' => $record->getFormSlug() ?? '',
                        'orgSlug' => $record->getOrganizationSlug() ?? '',
                        'url' => $record->getUrl() ?? '',
                    ];
                } catch (\Throwable $t) {
                }
            }

            return $forms;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function searchFormsToday(string $city, int $pageSize = 20): array
    {
        $today = new \DateTime('today');

        $api = new AnnuaireApi(new Client(), $this->getConfig());

        $request = new HelloAssoApiV5CommonModelsDirectoryListFormsRequest([
            'form_types' => ['Event'],
            'form_cities' => [$city],
            'form_start_date_min' => $today,
            'form_end_date_min' => $today,
        ]);

        try {
            $result = $api->directoryFormsPost($pageSize, null, $request);
            $items = $result->getData() ?? [];

            $forms = [];
            foreach ($items as $item) {
                try {
                    $record = $item->getRecord();
                    if (!$record) continue;
                    $forms[] = [
                        'formType' => $record->getFormType() ?? '',
                        'formSlug' => $record->getFormSlug() ?? '',
                        'orgSlug' => $record->getOrganizationSlug() ?? '',
                        'url' => $record->getUrl() ?? '',
                    ];
                } catch (\Throwable $t) {
                }
            }

            return $forms;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getFormDetail(string $orgSlug, string $formType, string $formSlug): ?array
    {
        $api = new FormulairesApi(new Client(), $this->getConfig());

        try {
            $form = $api->organizationsOrganizationSlugFormsFormTypeFormSlugPublicGet(
                $orgSlug,
                $formType,
                $formSlug
            );

            $place = $form->getPlace();
            $tiers = $form->getTiers() ?? [];

            $tarifs = [];
            foreach ($tiers as $tier) {
                $price = $tier->getPrice();
                $label = $tier->getLabel() ?? '';
                if ($price !== null) {
                    $tarifs[] = [
                        'label' => $label,
                        'price' => $price / 100,
                    ];
                }
            }

            return [
                'title' => $form->getTitle() ?? '',
                'description' => strip_tags($form->getDescription() ?? ''),
                'startDate' => $form->getStartDate() ?? '',
                'endDate' => $form->getEndDate() ?? '',
                'formType' => $form->getFormType() ?? '',
                'orgName' => $form->getOrganizationName() ?? '',
                'url' => $form->getUrl() ?? '',
                'place' => $place ? [
                    'name' => $place->getName() ?? '',
                    'address' => $place->getAddress() ?? '',
                    'city' => $place->getCity() ?? '',
                    'zipCode' => $place->getZipCode() ?? '',
                ] : null,
                'tarifs' => $tarifs,
                'state' => $form->getState() ?? '',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
