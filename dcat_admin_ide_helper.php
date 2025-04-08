<?php

/**
 * A helper file for Dcat Admin, to provide autocomplete information to your IDE
 *
 * This file should not be included in your code, only analyzed by your IDE!
 *
 * @author jqh <841324345@qq.com>
 */

namespace Dcat\Admin {
    use Illuminate\Support\Collection;

    /**
     * @property Grid\Column|Collection order
     * @property Grid\Column|Collection id
     * @property Grid\Column|Collection name
     * @property Grid\Column|Collection type
     * @property Grid\Column|Collection version
     * @property Grid\Column|Collection detail
     * @property Grid\Column|Collection created_at
     * @property Grid\Column|Collection updated_at
     * @property Grid\Column|Collection is_enabled
     * @property Grid\Column|Collection parent_id
     * @property Grid\Column|Collection icon
     * @property Grid\Column|Collection uri
     * @property Grid\Column|Collection extension
     * @property Grid\Column|Collection permission_id
     * @property Grid\Column|Collection menu_id
     * @property Grid\Column|Collection slug
     * @property Grid\Column|Collection http_method
     * @property Grid\Column|Collection http_path
     * @property Grid\Column|Collection role_id
     * @property Grid\Column|Collection user_id
     * @property Grid\Column|Collection value
     * @property Grid\Column|Collection username
     * @property Grid\Column|Collection password
     * @property Grid\Column|Collection avatar
     * @property Grid\Column|Collection remember_token
     * @property Grid\Column|Collection left
     * @property Grid\Column|Collection right
     * @property Grid\Column|Collection keywords
     * @property Grid\Column|Collection cover
     * @property Grid\Column|Collection setting
     * @property Grid\Column|Collection status
     * @property Grid\Column|Collection deleted_at
     * @property Grid\Column|Collection article_id
     * @property Grid\Column|Collection article_category_id
     * @property Grid\Column|Collection content
     * @property Grid\Column|Collection sort
     * @property Grid\Column|Collection file_id
     * @property Grid\Column|Collection assessable_type
     * @property Grid\Column|Collection assessable_id
     * @property Grid\Column|Collection file_path
     * @property Grid\Column|Collection keyword
     * @property Grid\Column|Collection published_at
     * @property Grid\Column|Collection expired_at
     * @property Grid\Column|Collection key
     * @property Grid\Column|Collection expiration
     * @property Grid\Column|Collection owner
     * @property Grid\Column|Collection uuid
     * @property Grid\Column|Collection connection
     * @property Grid\Column|Collection queue
     * @property Grid\Column|Collection payload
     * @property Grid\Column|Collection exception
     * @property Grid\Column|Collection failed_at
     * @property Grid\Column|Collection original_filename
     * @property Grid\Column|Collection filename
     * @property Grid\Column|Collection mime_type
     * @property Grid\Column|Collection path
     * @property Grid\Column|Collection size
     * @property Grid\Column|Collection hash
     * @property Grid\Column|Collection total_jobs
     * @property Grid\Column|Collection pending_jobs
     * @property Grid\Column|Collection failed_jobs
     * @property Grid\Column|Collection failed_job_ids
     * @property Grid\Column|Collection cancelled_at
     * @property Grid\Column|Collection finished_at
     * @property Grid\Column|Collection attempts
     * @property Grid\Column|Collection reserved_at
     * @property Grid\Column|Collection available_at
     * @property Grid\Column|Collection email
     * @property Grid\Column|Collection token
     * @property Grid\Column|Collection tokenable_type
     * @property Grid\Column|Collection tokenable_id
     * @property Grid\Column|Collection abilities
     * @property Grid\Column|Collection last_used_at
     * @property Grid\Column|Collection expires_at
     * @property Grid\Column|Collection ip_address
     * @property Grid\Column|Collection user_agent
     * @property Grid\Column|Collection last_activity
     * @property Grid\Column|Collection team_id
     * @property Grid\Column|Collection role
     * @property Grid\Column|Collection reference
     * @property Grid\Column|Collection personal_team
     * @property Grid\Column|Collection locale
     * @property Grid\Column|Collection original_text
     * @property Grid\Column|Collection translated_text
     * @property Grid\Column|Collection translator
     * @property Grid\Column|Collection call_stack
     * @property Grid\Column|Collection used_count
     * @property Grid\Column|Collection last_used
     * @property Grid\Column|Collection email_verified_at
     * @property Grid\Column|Collection two_factor_secret
     * @property Grid\Column|Collection two_factor_recovery_codes
     * @property Grid\Column|Collection two_factor_confirmed_at
     * @property Grid\Column|Collection current_team_id
     * @property Grid\Column|Collection profile_photo_path
     *
     * @method Grid\Column|Collection order(string $label = null)
     * @method Grid\Column|Collection id(string $label = null)
     * @method Grid\Column|Collection name(string $label = null)
     * @method Grid\Column|Collection type(string $label = null)
     * @method Grid\Column|Collection version(string $label = null)
     * @method Grid\Column|Collection detail(string $label = null)
     * @method Grid\Column|Collection created_at(string $label = null)
     * @method Grid\Column|Collection updated_at(string $label = null)
     * @method Grid\Column|Collection is_enabled(string $label = null)
     * @method Grid\Column|Collection parent_id(string $label = null)
     * @method Grid\Column|Collection icon(string $label = null)
     * @method Grid\Column|Collection uri(string $label = null)
     * @method Grid\Column|Collection extension(string $label = null)
     * @method Grid\Column|Collection permission_id(string $label = null)
     * @method Grid\Column|Collection menu_id(string $label = null)
     * @method Grid\Column|Collection slug(string $label = null)
     * @method Grid\Column|Collection http_method(string $label = null)
     * @method Grid\Column|Collection http_path(string $label = null)
     * @method Grid\Column|Collection role_id(string $label = null)
     * @method Grid\Column|Collection user_id(string $label = null)
     * @method Grid\Column|Collection value(string $label = null)
     * @method Grid\Column|Collection username(string $label = null)
     * @method Grid\Column|Collection password(string $label = null)
     * @method Grid\Column|Collection avatar(string $label = null)
     * @method Grid\Column|Collection remember_token(string $label = null)
     * @method Grid\Column|Collection left(string $label = null)
     * @method Grid\Column|Collection right(string $label = null)
     * @method Grid\Column|Collection keywords(string $label = null)
     * @method Grid\Column|Collection cover(string $label = null)
     * @method Grid\Column|Collection setting(string $label = null)
     * @method Grid\Column|Collection status(string $label = null)
     * @method Grid\Column|Collection deleted_at(string $label = null)
     * @method Grid\Column|Collection article_id(string $label = null)
     * @method Grid\Column|Collection article_category_id(string $label = null)
     * @method Grid\Column|Collection content(string $label = null)
     * @method Grid\Column|Collection sort(string $label = null)
     * @method Grid\Column|Collection file_id(string $label = null)
     * @method Grid\Column|Collection assessable_type(string $label = null)
     * @method Grid\Column|Collection assessable_id(string $label = null)
     * @method Grid\Column|Collection file_path(string $label = null)
     * @method Grid\Column|Collection keyword(string $label = null)
     * @method Grid\Column|Collection published_at(string $label = null)
     * @method Grid\Column|Collection expired_at(string $label = null)
     * @method Grid\Column|Collection key(string $label = null)
     * @method Grid\Column|Collection expiration(string $label = null)
     * @method Grid\Column|Collection owner(string $label = null)
     * @method Grid\Column|Collection uuid(string $label = null)
     * @method Grid\Column|Collection connection(string $label = null)
     * @method Grid\Column|Collection queue(string $label = null)
     * @method Grid\Column|Collection payload(string $label = null)
     * @method Grid\Column|Collection exception(string $label = null)
     * @method Grid\Column|Collection failed_at(string $label = null)
     * @method Grid\Column|Collection original_filename(string $label = null)
     * @method Grid\Column|Collection filename(string $label = null)
     * @method Grid\Column|Collection mime_type(string $label = null)
     * @method Grid\Column|Collection path(string $label = null)
     * @method Grid\Column|Collection size(string $label = null)
     * @method Grid\Column|Collection hash(string $label = null)
     * @method Grid\Column|Collection total_jobs(string $label = null)
     * @method Grid\Column|Collection pending_jobs(string $label = null)
     * @method Grid\Column|Collection failed_jobs(string $label = null)
     * @method Grid\Column|Collection failed_job_ids(string $label = null)
     * @method Grid\Column|Collection cancelled_at(string $label = null)
     * @method Grid\Column|Collection finished_at(string $label = null)
     * @method Grid\Column|Collection attempts(string $label = null)
     * @method Grid\Column|Collection reserved_at(string $label = null)
     * @method Grid\Column|Collection available_at(string $label = null)
     * @method Grid\Column|Collection email(string $label = null)
     * @method Grid\Column|Collection token(string $label = null)
     * @method Grid\Column|Collection tokenable_type(string $label = null)
     * @method Grid\Column|Collection tokenable_id(string $label = null)
     * @method Grid\Column|Collection abilities(string $label = null)
     * @method Grid\Column|Collection last_used_at(string $label = null)
     * @method Grid\Column|Collection expires_at(string $label = null)
     * @method Grid\Column|Collection ip_address(string $label = null)
     * @method Grid\Column|Collection user_agent(string $label = null)
     * @method Grid\Column|Collection last_activity(string $label = null)
     * @method Grid\Column|Collection team_id(string $label = null)
     * @method Grid\Column|Collection role(string $label = null)
     * @method Grid\Column|Collection reference(string $label = null)
     * @method Grid\Column|Collection personal_team(string $label = null)
     * @method Grid\Column|Collection locale(string $label = null)
     * @method Grid\Column|Collection original_text(string $label = null)
     * @method Grid\Column|Collection translated_text(string $label = null)
     * @method Grid\Column|Collection translator(string $label = null)
     * @method Grid\Column|Collection call_stack(string $label = null)
     * @method Grid\Column|Collection used_count(string $label = null)
     * @method Grid\Column|Collection last_used(string $label = null)
     * @method Grid\Column|Collection email_verified_at(string $label = null)
     * @method Grid\Column|Collection two_factor_secret(string $label = null)
     * @method Grid\Column|Collection two_factor_recovery_codes(string $label = null)
     * @method Grid\Column|Collection two_factor_confirmed_at(string $label = null)
     * @method Grid\Column|Collection current_team_id(string $label = null)
     * @method Grid\Column|Collection profile_photo_path(string $label = null)
     */
    class Grid {}

    class MiniGrid extends Grid {}

    /**
     * @property Show\Field|Collection order
     * @property Show\Field|Collection id
     * @property Show\Field|Collection name
     * @property Show\Field|Collection type
     * @property Show\Field|Collection version
     * @property Show\Field|Collection detail
     * @property Show\Field|Collection created_at
     * @property Show\Field|Collection updated_at
     * @property Show\Field|Collection is_enabled
     * @property Show\Field|Collection parent_id
     * @property Show\Field|Collection icon
     * @property Show\Field|Collection uri
     * @property Show\Field|Collection extension
     * @property Show\Field|Collection permission_id
     * @property Show\Field|Collection menu_id
     * @property Show\Field|Collection slug
     * @property Show\Field|Collection http_method
     * @property Show\Field|Collection http_path
     * @property Show\Field|Collection role_id
     * @property Show\Field|Collection user_id
     * @property Show\Field|Collection value
     * @property Show\Field|Collection username
     * @property Show\Field|Collection password
     * @property Show\Field|Collection avatar
     * @property Show\Field|Collection remember_token
     * @property Show\Field|Collection left
     * @property Show\Field|Collection right
     * @property Show\Field|Collection keywords
     * @property Show\Field|Collection cover
     * @property Show\Field|Collection setting
     * @property Show\Field|Collection status
     * @property Show\Field|Collection deleted_at
     * @property Show\Field|Collection article_id
     * @property Show\Field|Collection article_category_id
     * @property Show\Field|Collection content
     * @property Show\Field|Collection sort
     * @property Show\Field|Collection file_id
     * @property Show\Field|Collection assessable_type
     * @property Show\Field|Collection assessable_id
     * @property Show\Field|Collection file_path
     * @property Show\Field|Collection keyword
     * @property Show\Field|Collection published_at
     * @property Show\Field|Collection expired_at
     * @property Show\Field|Collection key
     * @property Show\Field|Collection expiration
     * @property Show\Field|Collection owner
     * @property Show\Field|Collection uuid
     * @property Show\Field|Collection connection
     * @property Show\Field|Collection queue
     * @property Show\Field|Collection payload
     * @property Show\Field|Collection exception
     * @property Show\Field|Collection failed_at
     * @property Show\Field|Collection original_filename
     * @property Show\Field|Collection filename
     * @property Show\Field|Collection mime_type
     * @property Show\Field|Collection path
     * @property Show\Field|Collection size
     * @property Show\Field|Collection hash
     * @property Show\Field|Collection total_jobs
     * @property Show\Field|Collection pending_jobs
     * @property Show\Field|Collection failed_jobs
     * @property Show\Field|Collection failed_job_ids
     * @property Show\Field|Collection cancelled_at
     * @property Show\Field|Collection finished_at
     * @property Show\Field|Collection attempts
     * @property Show\Field|Collection reserved_at
     * @property Show\Field|Collection available_at
     * @property Show\Field|Collection email
     * @property Show\Field|Collection token
     * @property Show\Field|Collection tokenable_type
     * @property Show\Field|Collection tokenable_id
     * @property Show\Field|Collection abilities
     * @property Show\Field|Collection last_used_at
     * @property Show\Field|Collection expires_at
     * @property Show\Field|Collection ip_address
     * @property Show\Field|Collection user_agent
     * @property Show\Field|Collection last_activity
     * @property Show\Field|Collection team_id
     * @property Show\Field|Collection role
     * @property Show\Field|Collection reference
     * @property Show\Field|Collection personal_team
     * @property Show\Field|Collection locale
     * @property Show\Field|Collection original_text
     * @property Show\Field|Collection translated_text
     * @property Show\Field|Collection translator
     * @property Show\Field|Collection call_stack
     * @property Show\Field|Collection used_count
     * @property Show\Field|Collection last_used
     * @property Show\Field|Collection email_verified_at
     * @property Show\Field|Collection two_factor_secret
     * @property Show\Field|Collection two_factor_recovery_codes
     * @property Show\Field|Collection two_factor_confirmed_at
     * @property Show\Field|Collection current_team_id
     * @property Show\Field|Collection profile_photo_path
     *
     * @method Show\Field|Collection order(string $label = null)
     * @method Show\Field|Collection id(string $label = null)
     * @method Show\Field|Collection name(string $label = null)
     * @method Show\Field|Collection type(string $label = null)
     * @method Show\Field|Collection version(string $label = null)
     * @method Show\Field|Collection detail(string $label = null)
     * @method Show\Field|Collection created_at(string $label = null)
     * @method Show\Field|Collection updated_at(string $label = null)
     * @method Show\Field|Collection is_enabled(string $label = null)
     * @method Show\Field|Collection parent_id(string $label = null)
     * @method Show\Field|Collection icon(string $label = null)
     * @method Show\Field|Collection uri(string $label = null)
     * @method Show\Field|Collection extension(string $label = null)
     * @method Show\Field|Collection permission_id(string $label = null)
     * @method Show\Field|Collection menu_id(string $label = null)
     * @method Show\Field|Collection slug(string $label = null)
     * @method Show\Field|Collection http_method(string $label = null)
     * @method Show\Field|Collection http_path(string $label = null)
     * @method Show\Field|Collection role_id(string $label = null)
     * @method Show\Field|Collection user_id(string $label = null)
     * @method Show\Field|Collection value(string $label = null)
     * @method Show\Field|Collection username(string $label = null)
     * @method Show\Field|Collection password(string $label = null)
     * @method Show\Field|Collection avatar(string $label = null)
     * @method Show\Field|Collection remember_token(string $label = null)
     * @method Show\Field|Collection left(string $label = null)
     * @method Show\Field|Collection right(string $label = null)
     * @method Show\Field|Collection keywords(string $label = null)
     * @method Show\Field|Collection cover(string $label = null)
     * @method Show\Field|Collection setting(string $label = null)
     * @method Show\Field|Collection status(string $label = null)
     * @method Show\Field|Collection deleted_at(string $label = null)
     * @method Show\Field|Collection article_id(string $label = null)
     * @method Show\Field|Collection article_category_id(string $label = null)
     * @method Show\Field|Collection content(string $label = null)
     * @method Show\Field|Collection sort(string $label = null)
     * @method Show\Field|Collection file_id(string $label = null)
     * @method Show\Field|Collection assessable_type(string $label = null)
     * @method Show\Field|Collection assessable_id(string $label = null)
     * @method Show\Field|Collection file_path(string $label = null)
     * @method Show\Field|Collection keyword(string $label = null)
     * @method Show\Field|Collection published_at(string $label = null)
     * @method Show\Field|Collection expired_at(string $label = null)
     * @method Show\Field|Collection key(string $label = null)
     * @method Show\Field|Collection expiration(string $label = null)
     * @method Show\Field|Collection owner(string $label = null)
     * @method Show\Field|Collection uuid(string $label = null)
     * @method Show\Field|Collection connection(string $label = null)
     * @method Show\Field|Collection queue(string $label = null)
     * @method Show\Field|Collection payload(string $label = null)
     * @method Show\Field|Collection exception(string $label = null)
     * @method Show\Field|Collection failed_at(string $label = null)
     * @method Show\Field|Collection original_filename(string $label = null)
     * @method Show\Field|Collection filename(string $label = null)
     * @method Show\Field|Collection mime_type(string $label = null)
     * @method Show\Field|Collection path(string $label = null)
     * @method Show\Field|Collection size(string $label = null)
     * @method Show\Field|Collection hash(string $label = null)
     * @method Show\Field|Collection total_jobs(string $label = null)
     * @method Show\Field|Collection pending_jobs(string $label = null)
     * @method Show\Field|Collection failed_jobs(string $label = null)
     * @method Show\Field|Collection failed_job_ids(string $label = null)
     * @method Show\Field|Collection cancelled_at(string $label = null)
     * @method Show\Field|Collection finished_at(string $label = null)
     * @method Show\Field|Collection attempts(string $label = null)
     * @method Show\Field|Collection reserved_at(string $label = null)
     * @method Show\Field|Collection available_at(string $label = null)
     * @method Show\Field|Collection email(string $label = null)
     * @method Show\Field|Collection token(string $label = null)
     * @method Show\Field|Collection tokenable_type(string $label = null)
     * @method Show\Field|Collection tokenable_id(string $label = null)
     * @method Show\Field|Collection abilities(string $label = null)
     * @method Show\Field|Collection last_used_at(string $label = null)
     * @method Show\Field|Collection expires_at(string $label = null)
     * @method Show\Field|Collection ip_address(string $label = null)
     * @method Show\Field|Collection user_agent(string $label = null)
     * @method Show\Field|Collection last_activity(string $label = null)
     * @method Show\Field|Collection team_id(string $label = null)
     * @method Show\Field|Collection role(string $label = null)
     * @method Show\Field|Collection reference(string $label = null)
     * @method Show\Field|Collection personal_team(string $label = null)
     * @method Show\Field|Collection locale(string $label = null)
     * @method Show\Field|Collection original_text(string $label = null)
     * @method Show\Field|Collection translated_text(string $label = null)
     * @method Show\Field|Collection translator(string $label = null)
     * @method Show\Field|Collection call_stack(string $label = null)
     * @method Show\Field|Collection used_count(string $label = null)
     * @method Show\Field|Collection last_used(string $label = null)
     * @method Show\Field|Collection email_verified_at(string $label = null)
     * @method Show\Field|Collection two_factor_secret(string $label = null)
     * @method Show\Field|Collection two_factor_recovery_codes(string $label = null)
     * @method Show\Field|Collection two_factor_confirmed_at(string $label = null)
     * @method Show\Field|Collection current_team_id(string $label = null)
     * @method Show\Field|Collection profile_photo_path(string $label = null)
     */
    class Show {}

    class Form {}

}

namespace Dcat\Admin\Grid {
    class Column {}

    class Filter {}
}

namespace Dcat\Admin\Show {
    class Field {}
}
