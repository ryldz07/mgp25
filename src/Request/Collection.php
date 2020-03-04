<?php

namespace InstagramAPI\Request;

use InstagramAPI\Response;

/**
 * Functions related to creating and managing collections of your saved media.
 *
 * To put media in a collection, you must first mark that media item as "saved".
 *
 * @see Media for functions related to saving/unsaving media items.
 * @see https://help.instagram.com/274531543007118
 */
class Collection extends RequestCollection
{
    /**
     * Get a list of all of your collections.
     *
     * @param string|null $maxId Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\GetCollectionsListResponse
     */
    public function getList(
        $maxId = null)
    {
        $request = $this->ig->request('collections/list/')
        ->addParam('collection_types', '["ALL_MEDIA_AUTO_COLLECTION","MEDIA","PRODUCT_AUTO_COLLECTION"]');
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\GetCollectionsListResponse());
    }

    /**
     * Get the feed of one of your collections.
     *
     * @param string      $collectionId The collection ID.
     * @param string|null $maxId        Next "maximum ID", used for pagination.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CollectionFeedResponse
     */
    public function getFeed(
        $collectionId,
        $maxId = null)
    {
        $request = $this->ig->request("feed/collection/{$collectionId}/");
        if ($maxId !== null) {
            $request->addParam('max_id', $maxId);
        }

        return $request->getResponse(new Response\CollectionFeedResponse());
    }

    /**
     * Create a new collection of your bookmarked (saved) media.
     *
     * @param string $name       Name of the collection.
     * @param string $moduleName (optional) From which app module (page) you're performing this action.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\CreateCollectionResponse
     */
    public function create(
        $name,
        $moduleName = 'collection_create')
    {
        return $this->ig->request('collections/create/')
            ->addPost('module_name', $moduleName)
            ->addPost('collection_visibility', '0') //Instagram is planning for public collections soon
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('name', $name)
            ->addPost('_uuid', $this->ig->uuid)
            ->getResponse(new Response\CreateCollectionResponse());
    }

    /**
     * Delete a collection.
     *
     * @param string $collectionId The collection ID.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\DeleteCollectionResponse
     */
    public function delete(
        $collectionId)
    {
        return $this->ig->request("collections/{$collectionId}/delete/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\DeleteCollectionResponse());
    }

    /**
     * Edit the name of a collection or add more saved media to an existing collection.
     *
     * @param string $collectionId The collection ID.
     * @param array  $params       User-provided key-value pairs:
     *                             string 'name',
     *                             string 'cover_media_id',
     *                             string[] 'add_media',
     *                             string 'module_name' (optional).
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\EditCollectionResponse
     */
    public function edit(
        $collectionId,
        array $params)
    {
        $postData = [];
        if (isset($params['name']) && $params['name'] !== '') {
            $postData['name'] = $params['name'];
        }
        if (!empty($params['cover_media_id'])) {
            $postData['cover_media_id'] = $params['cover_media_id'];
        }
        if (!empty($params['add_media']) && is_array($params['add_media'])) {
            $postData['added_media_ids'] = json_encode(array_values($params['add_media']));
            if (isset($params['module_name']) && $params['module_name'] !== '') {
                $postData['module_name'] = $params['module_name'];
            } else {
                $postData['module_name'] = 'feed_saved_add_to_collection';
            }
        }
        if (empty($postData)) {
            throw new \InvalidArgumentException('You must provide a name or at least one media ID.');
        }
        $request = $this->ig->request("collections/{$collectionId}/edit/")
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken());

        foreach ($postData as $key => $value) {
            $request->addPost($key, $value);
        }

        return $request->getResponse(new Response\EditCollectionResponse());
    }

    /**
     * Remove a single media item from one or more of your collections.
     *
     * Note that you can only remove a single media item per call, since this
     * function only accepts a single media ID.
     *
     * @param string[] $collectionIds Array with one or more collection IDs to remove the item from.
     * @param string   $mediaId       The media ID in Instagram's internal format (ie "3482384834_43294").
     * @param string   $moduleName    (optional) From which app module (page) you're performing this action.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\EditCollectionResponse
     */
    public function removeMedia(
        array $collectionIds,
        $mediaId,
        $moduleName = 'feed_contextual_saved_collections')
    {
        return $this->ig->request("media/{$mediaId}/save/")
            ->addPost('module_name', $moduleName)
            ->addPost('removed_collection_ids', json_encode(array_values($collectionIds)))
            ->addPost('radio_type', 'wifi-none')
            ->addPost('_uuid', $this->ig->uuid)
            ->addPost('_uid', $this->ig->account_id)
            ->addPost('_csrftoken', $this->ig->client->getToken())
            ->getResponse(new Response\EditCollectionResponse());
    }
}
