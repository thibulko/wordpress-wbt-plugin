
<div class="wrap">

    <?php if(!empty($errors)){ ?>
        <?php foreach($errors as $error){ ?>
            <div class="error settings-error notice is-dismissible">
                <p><strong><?php echo $error; ?></strong></p>
            </div>
        <?php } ?>
    <?php } ?>

    <?php if(!empty($success)){ ?>
        <?php foreach($success as $notice){ ?>
            <div class="updated settings-error notice is-dismissible">
                <p><strong><?php echo $notice; ?></strong></p>
            </div>
        <?php } ?>
    <?php } ?>

    <h1>WBT Settings</h1>

    <form method="POST" action="?page=wbt-settings&amp;action=add_key">
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                API KEY
            </th>

            <td>
                <input type="text" style="width: 350px" value="<?php echo !empty($api_key) ? $api_key : '';?>" name="api_key">
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class="button button-primary">Add</button>
            </td>
        </tr>
        <tr>
            <th>
                Export
                <p class="description">
                    Here you can send us your articles.
                </p>
            </th>
            <td>
                <a href="?page=wbt-settings&amp;action=export" class="button button-danger">Export to fn.com</a>
            </td>
        </tr>
        <tr>
            <th>
                Import
                <p class="description">
                    Here you can import your translates.
                </p>
            </th>

            <td>
                <a href="?page=wbt-settings&amp;action=import" class="button button-danger">Import from fn.com</a>
            </td>
        </tr>
        <?php if( !empty($wbt_languages) ): ?>
            <tr>
                <th>Languages</th>
                <td>
                    <table class="wp-list-table widefat fixed striped posts">
                        <thead>
                            <tr>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($wbt_languages as $language): ?>
                            <tr>
                                <td><?php echo $language['name']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
    </form>
</div>