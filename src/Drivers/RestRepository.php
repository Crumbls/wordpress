<?php

namespace Crumbls\WordPress\Drivers;

use Crumbls\WordPress\Contracts\WordPressDriver;
use Crumbls\WordPress\Exceptions\InvalidConfiguration;
use Crumbls\WordPress\Exceptions\Unauthorized;
use Crumbls\WordPress\Exceptions\UnexpectedRestResponse;
use GuzzleHttp\Client;

class RestRepository implements WordPressDriver
{
	protected $baseUri;
	protected Client $_client;
	protected array $config;

	protected array $_postTypes;


	public function __construct($app)
	{
		$this->config = (array)\Config::get('services.wordpress.wp-json', []);
	}
	
	public function getClient() : Client {
		if (!isset($this->_client)) {
			if (!is_array($this->config) || !array_key_exists('base_uri', $this->config) || !$this->config['base_uri']) {
				throw new InvalidConfiguration('services.wordpress.wp-json');
			}

			$this->_client = new Client($this->config);
		}
		return $this->_client;
	}

	public function getPosts(int $perPage = 10) : array {
		$client = $this->getClient();
		$url = 'posts';
		$response = $client->get($url, ['query' => ['per_page' => $perPage]]);

		return json_decode($response->getBody(), true);
	}

// Implement other methods from the WordPressDriver interface for additional endpoints or custom requests.
	public function getPostTypes(int $perPage = 1000): array
	{
		$client = $this->getClient();
		$url = 'types';

		$cacheKey = implode('::', [
			$url,
			$perPage
		]);

		return \Cache::remember($cacheKey, 60, function() use ($client, $url, $perPage) {
			$response = $client->get($url, [
				'query' => [
					'per_page' => $perPage
				]
			]);

			$body = json_decode($response->getBody(), true);

			/**
			 * Any other parsing goes here.
			 */

			return $body;
		});
	}

	public function getCategories(int $perPage = 10): array
	{
		return $this->execute('categories', ['query' => ['per_page' => $perPage]]);
	}

	public function getComments(int $perPage = 10): array {
		return $this->execute('comments', ['query' => ['per_page' => $perPage]]);
	}

	public function getPages(int $perPage = 10): array {
		return $this->execute('pages', ['query' => ['per_page' => $perPage]]);
	}

	public function getUsers(int $perPage = 10): array {
		$client = $this->getClient();

		$arguments = ['query' => ['per_page' => $perPage]];

		$cacheKey = implode('::', [
			__METHOD__,
			md5(json_encode($arguments))
		]);

		/**
		 * Switch this to use build in caching for Client, when enabled.
		 */
		return \Cache::remember($cacheKey, 60, function() use ($client, $arguments) {
			try {
				$response = $client->get('users', $arguments);

				$body = json_decode($response->getBody(), true);

				return $body;
			} catch (\GuzzleHttp\Exception\ClientException $e) {
				$code = $e->getCode();
				if ($code == 401) {
					throw new Unauthorized();
				}
				throw new UnexpectedRestResponse($e->getMessage());
				return null;
			}
		});
	}

	public function getByPostType(string $type, array $arguments) : array {
		if (!isset($this->_postTypes)) {
			$this->_postTypes = $this->getPostTypes(10000);
		}
		if (!array_key_exists($type, $this->_postTypes)) {
			throw new \Exception('Post type "'.$type.'" does not exist');
		}

		$postType = $this->_postTypes[$type];

		$uri = \Arr::first(array_filter(array_map(function($e) { return array_key_exists('href', $e) ? $e['href'] : null; }, $postType['_links']['wp:items'])));

		$client = $this->getClient();
		$config = $client->getConfig();

		$baseUri = null;

		if (array_key_exists('base_uri', $config)) {
			$baseUri = (string)$config['base_uri'].PHP_EOL;
			$x = strlen($uri);
			$uri = str_replace($baseUri, '', $uri);

			/**
			 * This is put in here to cover subdomain mis-configuration.
			 * We find that a lot of items, our base uri excludes www, when it should have it.
			 */
			if ($x == strlen($uri)) {
				$uri = preg_replace('#^https?:\/\/.*?\/wp-json\/wp\/v2\/#', '', $uri);
			}
		}

		$cacheKey = implode('::', [
			$baseUri,
			$uri,
			md5(json_encode($arguments))
		]);

		/**
		 * Switch this to use build in caching for Client, when enabled.
		 */
		return \Cache::remember($cacheKey, 60, function() use ($client, $uri, $arguments) {
			$response = $client->get($uri, $arguments);

			$body = json_decode($response->getBody(), true);

			return $body;
		});
	}

	protected function execute(string $endpoint, array $arguments = [], bool $cacheEnabled = true) : array|null {
		$exec = function($endpoint, $arguments) {
			$client = $this->getClient();
			try {
				$response = $client->get($endpoint, $arguments);
				$body = json_decode($response->getBody(), true);
				return $body;
			} catch (\GuzzleHttp\Exception\ClientException $e) {
				$code = $e->getCode();
				if ($code == 401) {
					throw new Unauthorized();
				}
				throw new UnexpectedRestResponse($e->getMessage());
			}
		};

		if ($cacheEnabled) {
			$cacheKey = implode('::', [
				$endpoint,
				md5(json_encode($arguments))
			]);
			echo $cacheKey;
			return \Cache::remember($cacheKey, 60 * 60, function() use ($endpoint, $arguments, $exec) {
				return $exec($endpoint, $arguments);
			});
		} else {
			return $exec($endpoint, $arguments);
		}
	}

	/**
	 * A simple way to handle getting custom post types.
	 * @param string|null $method
	 * @param array $arguments
	 * @return array
	 * @throws \Exception
	 */
	public function ffff__call(string $method = null, array $arguments = []) {
		if (!preg_match('#^(?:get)([A-Z].*?)$#i', $method, $short)) {
			throw new \Exception('Endpoint "'.$method.'" does not exist');
		}

		if (!isset($this->_postTypes)) {
			$this->_postTypes = $this->getPostTypes(10000);
		}

		$type = \Str::singular(strtolower($short[1]));

		if (!array_key_exists($type, $this->_postTypes)) {
			throw new \Exception('Post type "'.$type.'" does not exist');
		}

		return $this->getByPostType($type, $arguments);
	}

	public function getUser(int $userId = null): array|null
	{
		$client = $this->getClient();

		$cacheKey = implode('::', [
			__METHOD__,
			$userId
		]);

		/**
		 * Switch this to use build in caching for Client, when enabled.
		 */
		return \Cache::remember($cacheKey, 60, function() use ($client, $userId) {
			try {
				$response = $client->get('users/' . $userId);
				$body = json_decode($response->getBody(), true);
				return $body;
			} catch (\GuzzleHttp\Exception\ClientException $e) {
				$code = $e->getCode();
				if ($code == 401) {
					throw new Unauthorized();
				}
				throw new UnexpectedRestResponse($e->getMessage());
				return null;
			}
		});
	}

	public function getPageRevisions(int $pageId): array|null
	{
		// TODO: Implement getPageRevisions() method.
	}

	public function getPageRevision(int $pageId, int $revisionId): array|null
	{
		// TODO: Implement getPageRevision() method.
	}

	public function getPageAutosaves(int $pageId): array|null
	{
		// TODO: Implement getPageAutosaves() method.
	}

	public function getPageAutosave(int $pageId, int $autosaveId): array|null
	{
		// TODO: Implement getPageAutosave() method.
	}

	public function getMedias(int $perPage = 10): array
	{
		return $this->execute('media', ['query' => ['per_page' => $perPage]]);
	}

	public function getMediaPostProcess(int $mediaId): array|null
	{
		// TODO: Implement getMediaPostProcess() method.
	}

	public function getMediaEdit(int $mediaId): array|null
	{
		// TODO: Implement getMediaEdit() method.
	}

	public function getMenuItems(int $perPage = 10): array {
		return $this->execute('menu-items', ['query' => ['per_page' => $perPage]]);
	}

	public function getMenuItem(int $menuItemId): array|null
	{
		// TODO: Implement getMenuItem() method.
	}

	public function getMenuItemAutosaves(int $menuItemId): array|null
	{
		// TODO: Implement getMenuItemAutosaves() method.
	}

	public function getMenuItemAutosave(int $menUItemId, int $autosaveId): array|null
	{
		// TODO: Implement getMenuItemAutosave() method.
	}

	public function getBlocks(int $perPage = 10): array
	{
		return $this->execute('blocks', ['query' => ['per_page' => 100]]);
	}

	public function getBlock(int $blockid): array|null
	{
		// TODO: Implement getBlock() method.
	}

	public function getBlockRevisions(int $blockId): array|null
	{
		// TODO: Implement getBlockRevisions() method.
	}

	public function getBlockRevision(int $blockId, int $revisionId): array|null
	{
		// TODO: Implement getBlockRevision() method.
	}

	public function getBlockAutosaves(int $blockId): array|null
	{
		// TODO: Implement getBlockAutosaves() method.
	}

	public function getBlockAutosave(int $blockId, int $autosaveId): array|null
	{
		// TODO: Implement getBlockAutosave() method.
	}

	public function getTemplates(int $perPage = 10): array
	{
		return $this->execute('templates', ['query' => ['per_page' => $perPage]]);
	}

	public function getTemplatesLookup(): array
	{
		// TODO: Implement getTemplatesLookup() method.
	}

	public function getTemplate(int $templateId): array|null
	{
		// TODO: Implement getTemplate() method.
	}

	public function getTemplateRevisions(int $templateId): array|null
	{
		// TODO: Implement getTemplateRevisions() method.
	}

	public function getTemplateRevision(int $templateId, int $revisionid): array|null
	{
		// TODO: Implement getTemplateRevision() method.
	}

	public function getTemplateAutosaves(int $templateId): array|null
	{
		// TODO: Implement getTemplateAutosaves() method.
	}

	public function getTemplateAutosave(int $templateId, int $autosaveId): array|null
	{
		// TODO: Implement getTemplateAutosave() method.
	}

	public function getTemplateParts(int $perPage = 10): array
	{
		return $this->execute('template-parts', ['query' => ['per_page' => $perPage]]);
	}

	public function getTemplatePartsLookup(): array
	{
		// TODO: Implement getTemplatePartsLookup() method.
	}

	public function getTemplatePart(int $templatePartId): array|null
	{
		// TODO: Implement getTemplatePart() method.
	}

	public function getTemplatePartRevisions(int $templatePartId): array|null
	{
		// TODO: Implement getTemplatePartRevisions() method.
	}

	public function getTemplatePartRevision(int $templatePartId, $revisionId): array|null
	{
		// TODO: Implement getTemplatePartRevision() method.
	}

	public function getTemplatePartAutosaves(int $templatePartId): array|null
	{
		// TODO: Implement getTemplatePartAutosaves() method.
	}

	public function getTemplatePartAutosave(int $templatePartId, int $autosaveId): array|null
	{
		// TODO: Implement getTemplatePartAutosave() method.
	}

	public function getNavigations(int $perPage = 10): array {
		return $this->execute('navigation', ['query' => ['per_page' => $perPage]]);
	}

	public function getNavigation(int $navigationId): array|null
	{
		// TODO: Implement getNavigation() method.
	}

	public function getNavigationRevisions(int $navigationId): array|null
	{
		// TODO: Implement getNavigationRevisions() method.
	}

	public function getNavigationRevision(int $navigationId, int $revisionId): array|null
	{
		// TODO: Implement getNavigationRevision() method.
	}

	public function getNavigationAutosaves(int $navigationId): array|null
	{
		// TODO: Implement getNavigationAutosaves() method.
	}

	public function getNavigationAutosave(int $navigationId, int $autosaveId): array|null
	{
		// TODO: Implement getNavigationAutosave() method.
	}

	public function getTypes(int $perPage = 1000): array
	{
		return $this->execute('types', ['query' => ['per_page' => 100]]);
	}

	public function getType(int $typeId): array|null
	{
		// TODO: Implement getType() method.
	}

	public function getStatuses(int $perPage = 10): array
	{
		return $this->execute('statuses', ['query' => ['per_page' => $perPage]]);
	}

	public function getStatus(int $statusId): array|null
	{
		// TODO: Implement getStatus() method.
	}

	public function getTaxonomies(int $perPage = 10): array
	{
		return $this->execute('taxonomies', ['query' => ['per_page' => $perPage]]);
	}

	public function getTaxonomy(int $taxonomyId): array|null
	{
		// TODO: Implement getTaxonomy() method.
	}

	public function getTags(int $perPage = 10): array {
		return $this->execute('tags', ['query' => ['per_page' => $perPage]]);
	}

	public function getTag(int $tagId): array|null
	{
		// TODO: Implement getTag() method.
	}

	public function getMenus(int $perPage = 10): array
	{
		return $this->execute('menus', ['query' => ['per_page' => $perPage]]);
	}

	public function getMenu(int $menuId): array|null
	{
		// TODO: Implement getMenu() method.
	}

	public function getUserMe(): array|null
	{
		// TODO: Implement getUserMe() method.
	}

	public function getUserApplicationPasswords(int $userId): array|null
	{
		// TODO: Implement getUserApplicationPasswords() method.
	}

	public function getUserApplicationPasswordsIntrospect(int $userId): array|null
	{
		// TODO: Implement getUserApplicationPasswordsIntrospect() method.
	}

	public function getUserApplicationPassword(int $userId, int|string $passwordId): array|null
	{
		// TODO: Implement getUserApplicationPassword() method.
	}

	public function getComment(int $commentId): array|null
	{
		// TODO: Implement getComment() method.
	}

	public function getSearch(int $perPage = 10): array
	{
		// TODO: Implement getSearch() method.
	}

	public function getSettings(int $perPage = 10): array {
		return $this->execute('widgets', ['query' => ['per_page' => $perPage]]);
	}

	public function getThemes(int $perPage = 10): array
	{
		return $this->execute('blocks', ['query' => ['per_page' => 100]]);
	}

	public function getPlugins(int $perPage = 10): array
	{
		return $this->execute('plugins', ['query' => ['per_page' => 100]]);
	}

	public function getSidebars(): array {
		return $this->execute('widgets', ['query' => ['per_page' => 100]]);
	}

	public function getSidebar(int $id): array|null
	{
		// TODO: Implement getSidebar() method.
	}

	public function getWidgetTypes(int $perPage = 10): array
	{
		return $this->execute('widget-types', ['query' => ['per_page' => $perPage]]);
	}

	public function getWidgetType(int|string $widgetId): array|null
	{
		// TODO: Implement getWidgetType() method.
	}

	public function getWidgetTypeEncode(int|string $widgetId): array|null
	{
		// TODO: Implement getWidgetTypeEncode() method.
	}

	public function getWidgetTypeRender(int|string $widgetId): array|null
	{
		// TODO: Implement getWidgetTypeRender() method.
	}


	public function getWidgets(int $perPage = 10): array {
		return $this->execute('widgets', ['query' => ['per_page' => $perPage]]);
	}

	public function getWidget(int $widgetId): array|null
	{
		// TODO: Implement getWidget() method.
	}

	public function getMedia(int $mediaId): array|null
	{
		// TODO: Implement getMedia() method.
	}

	public function getPage(int $pageId): array|null
	{
		return $this->execute('pages/'.$pageId);
	}
}