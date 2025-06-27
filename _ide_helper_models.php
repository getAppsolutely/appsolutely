<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
    /**
     * @property string $slug
     * @property string $value
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminSetting whereValue($value)
     */
    class AdminSetting extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $title
     * @property string|null $keywords
     * @property string|null $description
     * @property string $content
     * @property string|null $slug
     * @property string|null $cover
     * @property array<array-key, mixed>|null $setting
     * @property int $status
     * @property int|null $sort
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\ArticleCategory> $categories
     * @property-read int|null $categories_count
     * @property-read string|null $content_formatted
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article slug(string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCover($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereExpiredAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereKeywords($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSetting($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article withoutTrashed()
     */
    class Article extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int|null $parent_id
     * @property int $left
     * @property int $right
     * @property string $title
     * @property string|null $keywords
     * @property string|null $description
     * @property string|null $slug
     * @property string|null $cover
     * @property array<array-key, mixed>|null $setting
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
     * @property-read int|null $articles_count
     * @property-read \Kalnoy\Nestedset\Collection<int, ArticleCategory> $children
     * @property-read int|null $children_count
     * @property-read ArticleCategory|null $parent
     *
     * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory ancestorsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory ancestorsOf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory applyNestedSetScope(?string $table = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory countErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory d()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory defaultOrder(string $dir = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory descendantsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory descendantsOf($id, array $columns = [], $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory fixSubtree($root)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory fixTree($root = null)
     * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory getNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory getPlainNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory getTotalErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory hasChildren()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory hasParent()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory isBroken()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory leaves(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory makeGap(int $cut, int $height)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory moveNode($key, $position)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory newModelQuery()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory onlyTrashed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory orWhereAncestorOf(bool $id, bool $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory orWhereDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory orWhereNodeBetween($values)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory orWhereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory ordered(string $direction = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory published(?\Carbon\Carbon $datetime = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory query()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory rebuildSubtree($root, array $data, $delete = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory rebuildTree(array $data, $delete = false, $root = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory reversed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory root(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory slug(string $slug)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory status($value = null, $operator = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereAncestorOf($id, $andSelf = false, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereAncestorOrSelf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereCover($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereCreatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDeletedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDescription($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereExpiredAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereId($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereIsAfter($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereIsBefore($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereIsLeaf()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereIsRoot()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereKeywords($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereLeft($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereParentId($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory wherePublishedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereRight($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereSetting($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereSlug($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereStatus($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereTitle($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereUpdatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory withDepth(string $as = 'depth')
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory withTrashed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory withoutRoot()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ArticleCategory withoutTrashed()
     */
    class ArticleCategory extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $file_id
     * @property string|null $assessable_type
     * @property string|null $assessable_id
     * @property string|null $type
     * @property string $file_path
     * @property string|null $title
     * @property string|null $keyword
     * @property string|null $description
     * @property string|null $content
     * @property string|null $setting
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\File $file
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereAssessableId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereAssessableType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereExpiredAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereFileId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereFilePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereKeyword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereSetting($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable whereUpdatedAt($value)
     */
    class Assessable extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $original_filename
     * @property string $filename
     * @property string $extension
     * @property string $mime_type
     * @property string $path
     * @property int $size
     * @property string $hash
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $full_path
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereExtension($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFilename($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereHash($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereMimeType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereOriginalFilename($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File wherePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereSize($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|File withoutTrashed()
     */
    class File extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $team_id
     * @property int $user_id
     * @property string|null $role
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereRole($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Membership whereUserId($value)
     */
    class Membership extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $left
     * @property int $right
     * @property int|null $parent_id
     * @property string $title
     * @property string $reference
     * @property string|null $remark
     * @property string|null $url
     * @property \App\Enums\MenuType $type
     * @property string|null $icon
     * @property string|null $thumbnail
     * @property array<array-key, mixed>|null $setting
     * @property string|null $permission_key
     * @property \App\Enums\MenuTarget $target
     * @property bool $is_external
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Kalnoy\Nestedset\Collection<int, Menu> $children
     * @property-read int|null $children_count
     * @property-read Menu|null $parent
     *
     * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu ancestorsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu ancestorsOf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu applyNestedSetScope(?string $table = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu countErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu d()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu defaultOrder(string $dir = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu descendantsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu descendantsOf($id, array $columns = [], $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu fixSubtree($root)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu fixTree($root = null)
     * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu getNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu getPlainNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu getTotalErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu hasChildren()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu hasParent()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu isBroken()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu leaves(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu makeGap(int $cut, int $height)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu moveNode($key, $position)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu newModelQuery()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu newQuery()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu orWhereAncestorOf(bool $id, bool $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu orWhereDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu orWhereNodeBetween($values)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu orWhereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu ordered(string $direction = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu published(?\Carbon\Carbon $datetime = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu query()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu rebuildSubtree($root, array $data, $delete = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu rebuildTree(array $data, $delete = false, $root = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu reference($value = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu reversed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu root(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu status($value = null, $operator = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereAncestorOf($id, $andSelf = false, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereAncestorOrSelf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereCreatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereExpiredAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereIcon($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereId($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereIsAfter($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereIsBefore($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereIsExternal($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereIsLeaf()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereIsRoot()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereLeft($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereParentId($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu wherePermissionKey($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu wherePublishedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereReference($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereRemark($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereRight($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereSetting($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereStatus($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereTarget($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereThumbnail($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereTitle($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereType($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereUpdatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu whereUrl($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu withDepth(string $as = 'depth')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|Menu withoutRoot()
     */
    final class Menu extends \Eloquent implements \Spatie\EloquentSortable\Sortable {}
}

namespace App\Models{
    /**
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Model newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Model newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Model query()
     */
    class Model extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property-read \Kalnoy\Nestedset\Collection<int, NestedSetModel> $children
     * @property-read int|null $children_count
     * @property-read NestedSetModel|null $parent
     * @property-write mixed $parent_id
     *
     * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel ancestorsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel ancestorsOf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel applyNestedSetScope(?string $table = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel countErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel d()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel defaultOrder(string $dir = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel descendantsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel descendantsOf($id, array $columns = [], $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel fixSubtree($root)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel fixTree($root = null)
     * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel getNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel getPlainNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel getTotalErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel hasChildren()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel hasParent()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel isBroken()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel leaves(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel makeGap(int $cut, int $height)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel moveNode($key, $position)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel newModelQuery()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel newQuery()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel orWhereAncestorOf(bool $id, bool $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel orWhereDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel orWhereNodeBetween($values)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel orWhereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel query()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel rebuildSubtree($root, array $data, $delete = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel rebuildTree(array $data, $delete = false, $root = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel reversed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel root(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereAncestorOf($id, $andSelf = false, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereAncestorOrSelf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereIsAfter($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereIsBefore($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereIsLeaf()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereIsRoot()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel whereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel withDepth(string $as = 'depth')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|NestedSetModel withoutRoot()
     */
    class NestedSetModel extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $user_id
     * @property string $reference
     * @property string $summary
     * @property int $amount
     * @property int $discounted_amount
     * @property int $total_amount
     * @property string|null $status
     * @property array<array-key, mixed>|null $delivery_info
     * @property string|null $note
     * @property string|null $remark
     * @property string|null $ip
     * @property array<array-key, mixed>|null $request
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
     * @property-read int|null $items_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderPayment> $payments
     * @property-read int|null $payments_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderShipment> $shipments
     * @property-read int|null $shipments_count
     * @property-read \App\Models\User|null $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryInfo($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountedAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereIp($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNote($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRequest($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSummary($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Order withoutTrashed()
     */
    class Order extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $order_id
     * @property int $product_id
     * @property int $product_sku_id
     * @property string $reference
     * @property string $summary
     * @property int $original_price
     * @property int $price
     * @property int $quantity
     * @property int $discounted_amount
     * @property int $amount
     * @property array<array-key, mixed>|null $product_snapshot
     * @property string|null $note
     * @property string|null $remark
     * @property string|null $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Order|null $order
     * @property-read \App\Models\Product|null $product
     * @property-read \App\Models\ProductSku|null $productSku
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereDiscountedAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereNote($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOriginalPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductSkuId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductSnapshot($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSummary($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem withoutTrashed()
     */
    class OrderItem extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $reference
     * @property int $order_id
     * @property int $payment_id
     * @property string|null $vendor
     * @property string|null $vendor_reference
     * @property array<array-key, mixed>|null $vendor_extra_info
     * @property int $payment_amount
     * @property string $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Order|null $order
     * @property-read \App\Models\Payment|null $payment
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereOrderId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePaymentAmount($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment wherePaymentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereVendor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereVendorExtraInfo($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment whereVendorReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderPayment withoutTrashed()
     */
    class OrderPayment extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $order_id
     * @property string $product_type
     * @property string|null $email
     * @property string|null $name
     * @property string|null $mobile
     * @property string|null $address
     * @property string|null $address_extra
     * @property string|null $town
     * @property string|null $city
     * @property string|null $province
     * @property string|null $postcode
     * @property string|null $country
     * @property string|null $delivery_vendor
     * @property string|null $delivery_reference
     * @property string|null $remark
     * @property string $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Order|null $order
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereAddressExtra($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereCity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereCountry($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereDeliveryReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereDeliveryVendor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereMobile($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereOrderId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment wherePostcode($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereProductType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereProvince($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereTown($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderShipment withoutTrashed()
     */
    class OrderShipment extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $reference
     * @property string|null $title
     * @property string|null $name
     * @property string|null $slug
     * @property string|null $keywords
     * @property string|null $description
     * @property string|null $content
     * @property array<array-key, mixed>|null $setting
     * @property string|null $canonical_url
     * @property string|null $meta_robots
     * @property string|null $og_title
     * @property string|null $og_description
     * @property string|null $og_image
     * @property array<array-key, mixed>|null $structured_data
     * @property string|null $hreflang
     * @property string|null $language
     * @property int|null $parent_id
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PageBlockSetting> $blocks
     * @property-read int|null $blocks_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page slug(string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCanonicalUrl($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereExpiredAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereHreflang($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereKeywords($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLanguage($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMetaRobots($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOgDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOgImage($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereOgTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSetting($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereStructuredData($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
     */
    class Page extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $block_group_id
     * @property string $title
     * @property string|null $reference
     * @property string $class
     * @property string|null $remark
     * @property string|null $description
     * @property string|null $template
     * @property string|null $instruction
     * @property string $scope
     * @property array<array-key, mixed>|null $schema
     * @property array<array-key, mixed>|null $schema_values
     * @property int|null $droppable
     * @property array<array-key, mixed>|null $setting
     * @property int $sort
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\PageBlockGroup $group
     * @property-read \App\Models\PageBlockValue|null $settingValue
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PageBlockSetting> $settings
     * @property-read int|null $settings_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereBlockGroupId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereClass($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereDroppable($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereInstruction($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereSchema($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereSchemaValues($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereScope($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereSetting($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereTemplate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlock whereUpdatedAt($value)
     */
    final class PageBlock extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $title
     * @property string|null $remark
     * @property int $sort
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PageBlock> $blocks
     * @property-read int|null $blocks_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockGroup whereUpdatedAt($value)
     */
    final class PageBlockGroup extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $page_id
     * @property string|null $reference
     * @property int $block_id
     * @property int $block_value_id
     * @property string|null $type
     * @property string|null $remark
     * @property int $sort
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\PageBlock $block
     * @property-read \App\Models\PageBlockValue $blockValue
     * @property-read array $parameters
     * @property-read \App\Models\Page $page
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereBlockId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereBlockValueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereExpiredAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting wherePageId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockSetting whereUpdatedAt($value)
     */
    final class PageBlockSetting extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $block_id
     * @property string|null $template
     * @property string|null $scripts
     * @property string|null $stylesheets
     * @property array<array-key, mixed>|null $styles
     * @property array<array-key, mixed>|null $schema_values
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int|null $setting_id
     * @property-read \App\Models\PageBlock|null $block
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereBlockId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereSchemaValues($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereScripts($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereSettingId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereStyles($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereStylesheets($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereTemplate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PageBlockValue whereUpdatedAt($value)
     */
    final class PageBlockValue extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $reference
     * @property string $title
     * @property string $display
     * @property string|null $vendor
     * @property string|null $handler
     * @property string|null $device
     * @property string|null $merchant_id
     * @property string|null $merchant_key
     * @property string|null $merchant_secret
     * @property array<array-key, mixed>|null $setting
     * @property string|null $instruction
     * @property string|null $remark
     * @property int|null $sort
     * @property int $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDevice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDisplay($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereHandler($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereInstruction($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMerchantId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMerchantKey($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMerchantSecret($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSetting($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereVendor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withoutTrashed()
     */
    class Payment extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $type
     * @property array<array-key, mixed>|null $shipment_methods
     * @property string $slug
     * @property string $title
     * @property string|null $subtitle
     * @property string|null $cover
     * @property string|null $keywords
     * @property string|null $description
     * @property string|null $content
     * @property int|null $original_price
     * @property int|null $price
     * @property array<array-key, mixed>|null $setting
     * @property array<array-key, mixed>|null $payment_methods
     * @property array<array-key, mixed>|null $additional_columns
     * @property int|null $sort
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\ProductCategory> $categories
     * @property-read int|null $categories_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSku> $skus
     * @property-read int|null $skus_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product slug(string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereAdditionalColumns($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCover($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereExpiredAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereKeywords($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereOriginalPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePaymentMethods($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSetting($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereShipmentMethods($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSubtitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
     */
    class Product extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $title
     * @property string $slug
     * @property string|null $remark
     * @property bool $status
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductAttributeGroup> $attributeGroups
     * @property-read int|null $attribute_groups_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductAttributeValue> $values
     * @property-read int|null $values_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute slug(string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute withoutTrashed()
     */
    class ProductAttribute extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $title
     * @property string|null $remark
     * @property bool $status
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductAttribute> $attributes
     * @property-read int|null $attributes_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeGroup withoutTrashed()
     */
    class ProductAttributeGroup extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $product_attribute_id
     * @property string $value
     * @property string $slug
     * @property bool $status
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\ProductAttribute|null $attribute
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductSku> $productSkus
     * @property-read int|null $product_skus_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue slug(string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue whereProductAttributeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue whereValue($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttributeValue withoutTrashed()
     */
    class ProductAttributeValue extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int|null $parent_id
     * @property int $left
     * @property int $right
     * @property string $title
     * @property string|null $keywords
     * @property string|null $description
     * @property string|null $slug
     * @property string|null $cover
     * @property array<array-key, mixed>|null $setting
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Kalnoy\Nestedset\Collection<int, ProductCategory> $children
     * @property-read int|null $children_count
     * @property-read ProductCategory|null $parent
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
     * @property-read int|null $products_count
     *
     * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory ancestorsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory ancestorsOf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory applyNestedSetScope(?string $table = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory countErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory d()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory defaultOrder(string $dir = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory descendantsAndSelf($id, array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory descendantsOf($id, array $columns = [], $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory fixSubtree($root)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory fixTree($root = null)
     * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory getNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory getPlainNodeData($id, $required = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory getTotalErrors()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory hasChildren()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory hasParent()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory isBroken()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory leaves(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory makeGap(int $cut, int $height)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory moveNode($key, $position)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory newModelQuery()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory onlyTrashed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory orWhereAncestorOf(bool $id, bool $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory orWhereDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory orWhereNodeBetween($values)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory orWhereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory ordered(string $direction = 'asc')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory published(?\Carbon\Carbon $datetime = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory query()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory rebuildSubtree($root, array $data, $delete = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory rebuildTree(array $data, $delete = false, $root = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory reversed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory root(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory slug(string $slug)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory status($value = null, $operator = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereAncestorOf($id, $andSelf = false, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereAncestorOrSelf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereCover($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereCreatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereDeletedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereDescription($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereExpiredAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereId($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereIsAfter($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereIsBefore($id, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereIsLeaf()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereIsRoot()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereKeywords($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereLeft($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereNotDescendantOf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereParentId($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory wherePublishedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereRight($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereSetting($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereSlug($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereStatus($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereTitle($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory whereUpdatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory withDepth(string $as = 'depth')
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory withTrashed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ProductCategory withoutRoot()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductCategory withoutTrashed()
     */
    class ProductCategory extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $product_id
     * @property array<array-key, mixed>|null $attributes
     * @property string $slug
     * @property string $title
     * @property string|null $subtitle
     * @property string|null $cover
     * @property string|null $keywords
     * @property string|null $description
     * @property string|null $content
     * @property int $stock
     * @property int|null $original_price
     * @property int $price
     * @property int|null $sort
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductAttributeValue> $attributeValues
     * @property-read int|null $attribute_values_count
     * @property-read \App\Models\Product $product
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku ordered(string $direction = 'asc')
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku slug(string $slug)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereAttributes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereCover($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereExpiredAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereKeywords($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereOriginalPrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku wherePrice($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereProductId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereStock($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereSubtitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductSku withoutTrashed()
     */
    class ProductSku extends \Eloquent implements \Spatie\EloquentSortable\Sortable {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $version_id
     * @property string $platform
     * @property string|null $arch
     * @property int $force_update
     * @property array<array-key, mixed>|null $gray_strategy
     * @property string|null $release_notes
     * @property string|null $build_status
     * @property string|null $build_log
     * @property string|null $path
     * @property string|null $signature
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Assessable|null $assessable
     * @property-read string|null $download_url
     * @property-read \App\Models\ReleaseVersion $version
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereArch($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereBuildLog($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereBuildStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereForceUpdate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereGrayStrategy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild wherePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild wherePlatform($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereReleaseNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereSignature($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild whereVersionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseBuild withoutTrashed()
     */
    final class ReleaseBuild extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $version
     * @property string|null $remark
     * @property string|null $release_channel
     * @property int $status
     * @property \Illuminate\Support\Carbon $published_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReleaseBuild> $builds
     * @property-read int|null $builds_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion published(?\Carbon\Carbon $datetime = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion status($value = null, $operator = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion wherePublishedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereReleaseChannel($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion whereVersion($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ReleaseVersion withoutTrashed()
     */
    final class ReleaseVersion extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $user_id
     * @property string|null $reference
     * @property string $name
     * @property bool $personal_team
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\User|null $owner
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamInvitation> $teamInvitations
     * @property-read int|null $team_invitations_count
     * @property-read \App\Models\Membership|null $membership
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
     * @property-read int|null $users_count
     *
     * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team reference($value = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePersonalTeam($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereReference($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withoutTrashed()
     */
    class Team extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $team_id
     * @property string $email
     * @property string|null $role
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Team $team
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereRole($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereUpdatedAt($value)
     */
    class TeamInvitation extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $locale
     * @property string $type
     * @property string $original_text
     * @property string|null $translated_text
     * @property string|null $translator
     * @property string|null $call_stack
     * @property int $used_count
     * @property \Illuminate\Support\Carbon $last_used
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereCallStack($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereLastUsed($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereLocale($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereOriginalText($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereTranslatedText($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereTranslator($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Translation whereUsedCount($value)
     */
    class Translation extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $two_factor_secret
     * @property string|null $two_factor_recovery_codes
     * @property string|null $two_factor_confirmed_at
     * @property string|null $remember_token
     * @property int|null $current_team_id
     * @property string|null $profile_photo_path
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Team|null $currentTeam
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $ownedTeams
     * @property-read int|null $owned_teams_count
     * @property-read string $profile_photo_url
     * @property-read \App\Models\Membership|null $membership
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
     * @property-read int|null $teams_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
     * @property-read int|null $tokens_count
     *
     * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
     */
    class User extends \Eloquent {}
}

namespace App\Models{
    /**
     * @property int $id
     * @property int $user_id
     * @property string|null $name
     * @property string|null $mobile
     * @property string|null $address
     * @property string|null $address_extra
     * @property string|null $town
     * @property string|null $city
     * @property string|null $province
     * @property string|null $postcode
     * @property string|null $country
     * @property string|null $note
     * @property string|null $remark
     * @property int|null $sort
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\User|null $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereAddressExtra($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCity($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCountry($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereMobile($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereNote($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress wherePostcode($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereProvince($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereRemark($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereTown($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAddress withoutTrashed()
     */
    class UserAddress extends \Eloquent {}
}
