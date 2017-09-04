
<div class="wrap">
    <?php if(!empty($messages)): ?>
        <?php if(!empty($messages['errors'])): ?>
            <div class="error settings-error notice is-dismissible">
                <?php foreach($messages['errors'] as $error): ?>
                    <p><strong><?php echo $error; ?></strong></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($messages['success'])): ?>
            <div class="updated settings-error notice is-dismissible">
                <?php foreach($messages['success'] as $success): ?>
                    <p><strong><?php echo $success; ?></strong></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <h1>WBTranslator Settings</h1>

    <form method="POST" action="?page=wbt-settings&action=types">
        <table class="form-table">
            <tbody>
            <tr>
                <th>What to translate?</th>
                <td>
                    <ul>
                        <li>
                            <label>
                                <input type="checkbox" name="types[]" value="<?php print WbtAdmin::TYPE_THEME; ?>" <?php if(in_array(WbtAdmin::TYPE_THEME, $types)): ?>checked="checked"<?php endif; ?>>
                                Theme (<?php print wp_get_theme(); ?>)
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" name="types[]" value="<?php print WbtAdmin::TYPE_POSTS; ?>" <?php if(in_array(WbtAdmin::TYPE_POSTS, $types)): ?>checked="checked"<?php endif; ?>>
                                Posts, Pages
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="checkbox" name="types[]" value="<?php print WbtAdmin::TYPE_TERMS; ?>" <?php if(in_array(WbtAdmin::TYPE_TERMS, $types)): ?>checked="checked"<?php endif; ?>>
                                Categories, Tags
                            </label>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><button class="button button-primary">Save</button></td>
            </tr>
            </tbody>
        </table>
    </form>

    <hr>

    <form method="POST" action="?page=wbt-settings&action=add_key">
        <table class="form-table">
            <tbody>
                <tr>
                    <th>API KEY</th>
                    <td><input type="text" style="width: 350px" value="<?php echo !empty($wbt_api_key) ? $wbt_api_key : ''; ?>" name="api_key"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><button class="button button-primary">Add</button></td>
                </tr>
                <tr>
                    <th>Default Language</th>
                    <td>
                        <?php if(!empty($wbt_default_language)): ?>
                            <?php echo $wbt_default_language['name']; ?> (<?php echo $wbt_default_language['code']; ?>)
                        <?php endif ?>
                    </td>
                </tr>
                <tr>
                    <th>Languages</th>
                    <td>
                        <?php if(!empty($wbt_languages)): ?>
                        <ul>
                            <?php foreach($wbt_languages as $language): ?>
                                <li><?php echo $language['name']; ?> (<?php echo $language['code']; ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <hr>

    <table class="form-table">
        <tbody>
            <tr>
                <th>
                    Export
                    <p class="description">Here you can send us your articles.</p>
                </th>
                <td>
                    <a href="?page=wbt-settings&amp;action=export" class="button button-danger">Export to WBTranslator</a>
                </td>
            </tr>
            <tr>
                <th>
                    Import
                    <p class="description">Here you can import your translates.</p>
                </th>
                <td>
                    <a href="?page=wbt-settings&amp;action=import" class="button button-danger">Import from WBTranslator</a>
                </td>
            </tr>
        </tbody>
    </table>

</div>