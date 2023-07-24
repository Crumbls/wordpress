<?php


namespace Crumbls\WordPress\Contracts;

use Illuminate\Foundation\Application;

interface WordPressDriver
{
	public function __construct(Application $app);

	/**
	 * @param string $postType
	 * @param array $arguments
	 * @return array
	 */
	public function getByPostType(string $postType, array $arguments) : array;

	/**
	 *
	 */
	/*
	public function getCategories(int $perPage = 10) : array;
	public function getComments(int $perPage = 10) : array;
*/

	/**
	 * Pages
	 */
	public function getPages(int $perPage = 10) : array;
	public function getPageRevisions(int $pageId) : array|null;
	public function getPageRevision(int $pageId, int $revisionId) : array|null;
	public function getPageAutosaves(int $pageId) : array|null;
	public function getPageAutosave(int $pageId, int $autosaveId) : array|null;

	/**
	 * Media
	 */
	public function getMedias(int $perPage = 10) : array;
	public function getMedia(int $mediaId) : array|null;
	public function getMediaPostProcess(int $mediaId) : array|null;
	public function getMediaEdit(int $mediaId) : array|null;

	/**
	 * Menu Items
	 */
	public function getMenuItems(int $perPage = 10) : array;
	public function getMenuItem(int $menuItemId) : array|null;
	public function getMenuItemAutosaves(int $menuItemId) : array|null;
	public function getMenuItemAutosave(int $menUItemId, int $autosaveId) : array|null;

	/**
	 * Blocks
	 */
	public function getBlocks(int $perPage = 10) : array;
	public function getBlock(int $blockid) : array|null;
	public function getBlockRevisions(int $blockId) : array|null;
	public function getBlockRevision(int $blockId, int $revisionId) : array|null;
	public function getBlockAutosaves(int $blockId) : array|null;
	public function getBlockAutosave(int $blockId, int $autosaveId) : array|null;

	/**
	 * Templates
	 */
	public function getTemplates(int $perPage = 10) : array;
	public function getTemplatesLookup() : array;
	public function getTemplate(int $templateId) : array|null;
	public function getTemplateRevisions(int $templateId) : array|null;
	public function getTemplateRevision(int $templateId, int $revisionid) : array|null;
	public function getTemplateAutosaves(int $templateId) : array|null;
	public function getTemplateAutosave(int $templateId, int $autosaveId) : array|null;

	/**
	 * Template Parts
	 */
	public function getTemplateParts(int $perPage = 10) : array;
	public function getTemplatePartsLookup() : array;
	public function getTemplatePart(int $templatePartId) : array|null;
	public function getTemplatePartRevisions(int $templatePartId) : array|null;
	public function getTemplatePartRevision(int $templatePartId, $revisionId) : array|null;
	public function getTemplatePartAutosaves(int $templatePartId) : array|null;
	public function getTemplatePartAutosave(int $templatePartId, int $autosaveId) : array|null;

	/**
	 * Navigation
	 */
	public function getNavigations(int $perPage = 10) : array;
	public function getNavigation(int $navigationId) : array|null;
	public function getNavigationRevisions(int $navigationId) : array|null;
	public function getNavigationRevision(int $navigationId, int $revisionId) : array|null;
	public function getNavigationAutosaves(int $navigationId) : array|null;
	public function getNavigationAutosave(int $navigationId, int $autosaveId) : array|null;

	/**
	 * Post Types
	 */
	public function getTypes(int $perPage = 1000) : array;
	public function getType(int $typeId) : array|null;

	/**
	 * Statuses
	 */
	public function getStatuses(int $perPage = 10) : array;
	public function getStatus(int $statusId) : array|null;

	/**
	 * Taxonomies
	 */
	public function getTaxonomies(int $perPage = 10) : array;
	public function getTaxonomy(int $taxonomyId) : array|null;

	/**
	 * Tags
	 */

	/**
	 * @param int $perPage
	 * @return array
	 */
	public function getTags(int $perPage = 10) : array;

	/**
	 * @param int $tagId
	 * @return array|null
	 */
	public function getTag(int $tagId) : array|null;

	/**
	 * Menus
	 */

	/**
	 * @param int $perPage
	 * @return array
	 */
	public function getMenus(int $perPage = 10) : array;
	/**
	 * @param int $menuId
	 * @return array|null
	 */
	public function getMenu(int $menuId) : array|null;

	/**
	 * Users
	 */

	/**
	 * @param int $perPage
	 * @return array
	 */
	public function getUsers(int $perPage = 10) : array;
	public function getUser(int $userId) : array|null;
	public function getUserMe() : array|null;
	public function getUserApplicationPasswords(int $userId) : array|null;
	public function getUserApplicationPasswordsIntrospect(int $userId) : array|null;
	public function getUserApplicationPassword(int $userId, int|string $passwordId) : array|null;


	/**
	 * Get all comments.
	 * @param int $perPage
	 * @return array
	 */
	public function getComments(int $perPage = 10) : array;

	/**
	 * Get a single comment
	 * @param int $commentId
	 * @return array|null
	 */
	public function getComment(int $commentId) : array|null;

	/**
	 * Search
	 * @param int $perPage
	 * @return array
	 */
	public function getSearch(int $perPage = 10) : array;

	/**
	 * Get Block Renered
	 */
	/*
	/wp/v2/block-renderer/(?P<name>[a-z0-9-]+/[a-z0-9-]+): {},

	#Block Types Block Types
/wp/v2/block-types: {},
/wp/v2/block-types/(?P<namespace>[a-zA-Z0-9_-]+): {},
/wp/v2/block-types/(?P<namespace>[a-zA-Z0-9_-]+)/(?P<name>[a-zA-Z0-9_-]+): {},
Top ↑

#Global Styles Global Styles
/wp/v2/global-styles/themes/(?P<stylesheet>[\/\s%\w\.\(\)\[\]\@_\-]+)/variations: {},
/wp/v2/global-styles/themes/(?P<stylesheet>[^\/:<>\*\?"\|]+(?:\/[^\/:<>\*\?"\|]+)?): {},
/wp/v2/global-styles/(?P<id>[\/\w-]+): {},
Top ↑
*/
	/**
	 * Get all settings.
	 * @param int $perPage
	 * @return array
	 */
	public function getSettings(int $perPage = 10) : array;

	/**
	 * Get all themes.
	 * @param int $perPage
	 * @return array
	 */
	public function getThemes(int $perPage = 10) : array;

	/**
	 * Get all plugins.
	 * @param int $perPage
	 * @return array
	 */
	public function getPlugins(int $perPage = 10) : array;

	/**
	 * Get all sidebars
	 * @return array
	 */
	public function getSidebars() : array;

	/**
	 * Get a specific sidebar.
	 * @param int $id
	 * @return array|null
	 */
	public function getSidebar(int $id) : array|null;

	public function getWidgetTypes(int $perPage = 10) : array;
	public function getWidgetType(int|string $widgetId) : array|null;
	public function getWidgetTypeEncode(int|string $widgetId) : array|null;
	public function getWidgetTypeRender(int|string $widgetId) : array|null;


	public function getWidgets(int $perPage = 10) : array;
	public function getWidget(int $widgetId) : array|null;
}