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
     * @property string|null $setting
     * @property int $status
     * @property int|null $sort
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
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCover($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereKeywords($value)
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
     * @property string|null $setting
     * @property int $status
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
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory query()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory rebuildSubtree($root, array $data, $delete = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory rebuildTree(array $data, $delete = false, $root = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory reversed()
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory root(array $columns = [])
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory status($value = null, $operator = null)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereAncestorOf($id, $andSelf = false, $boolean = 'and')
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereAncestorOrSelf($id)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereCover($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereCreatedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDeletedAt($value)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
     * @method static \Kalnoy\Nestedset\QueryBuilder<static>|ArticleCategory whereDescription($value)
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
     * @property string $published_at
     * @property string|null $expired_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\File $file
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessable query()
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
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Model newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Model newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Model query()
     */
    class Model extends \Eloquent {}
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
