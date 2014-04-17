<?php

namespace Backend\Modules\{$camel_case_name}\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Model;
use Backend\Core\Engine\Language;

/**
 * In this file we store all generic functions that we will be using in the {$title} module
 *
 * @author {$author_name} <{$author_email}>
 */
class Model
{
    const QRY_DATAGRID_BROWSE =
        'SELECT i.id, i.{$meta_field}, UNIX_TIMESTAMP(i.created_on) AS created_on{$datagrid_extra}
         FROM {$underscored_name} AS i
         WHERE i.language = ?{$datagrid_order}';
{$datagrid_categories}
    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function delete($id)
    {
        Model::get('database')->delete('{$underscored_name}', 'id = ?', (int) $id);
    }
{$delete_category}{$delete_image}
    /**
     * Checks if a certain item exists
     *
     * @param int $id
     * @return bool
     */
    public static function exists($id)
    {
        return (bool) Model::get('database')->getVar(
            'SELECT 1
             FROM {$underscored_name} AS i
             WHERE i.id = ?
             LIMIT 1',
            array((int) $id)
        );
    }
{$exists_category}
    /**
     * Fetches a certain item
     *
     * @param int $id
     * @return array
     */
    public static function get($id)
    {
        return (array) Model::get('database')->getRecord(
            'SELECT i.*{$select_extra}
             FROM {$underscored_name} AS i
             WHERE i.id = ?',
            array((int) $id)
        );
    }
{$get_category}{$get_images}{$get_max_sequence}{$get_url}{$get_url_category}
    /**
     * Insert an item in the database
     *
     * @param array $item
     * @return int
     */
    public static function insert(array $item)
    {
        $item['created_on'] = Model::getUTCDate();
        $item['edited_on'] = Model::getUTCDate();

        return (int) Model::get('database')->insert('{$underscored_name}', $item);
    }
{$insert_category}{$insert_image}
    /**
     * Updates an item
     *
     * @param array $item
     */
    public static function update(array $item)
    {
        $item['edited_on'] = Model::getUTCDate();

        Model::get('database')->update(
            '{$underscored_name}', $item, 'id = ?', (int) $item['id']
        );
    }
{$update_category}{$update_image}}
