
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

    <h1>FWT Settings</h1>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                API KEY
            </th>

            <td>
                <form method="POST" action="?page=fwt-settings&amp;action=add_key">
                    <input type="text" style="width: 350px" value="<?php echo ( !empty($keys['api']) ? $keys['api'] : '' );?>" name="api_key">
            </td>
        </tr>
        <tr>
            <th>
                Secutiry key
            </th>

            <td>
                    <input type="text" style="width: 350px" value="<?php echo ( !empty($keys['security']) ? $keys['security'] : '' );?>" name="secret_key">
                    <br>
                    <button class="button button-primary">Save</button>
                </form
            </td>
        </tr>
        <tr>
            <th>
                Import / Export
                <p class="description">
                    Here you can send us your articles or sync all data.
                </p>
            </th>

            <td>
                <a href="?page=fwt-settings&amp;action=sync" class="button button-danger">Export to fn.com</a>
            </td>
        </tr>
        <?php if( !empty($fwt_languages) ){ ?>
            <tr>
                <th>Languages</th>
                <td>
                    <table class="wp-list-table widefat fixed striped posts">
                        <thead>
                        <tr>
                            <td>Name</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($fwt_languages as $language){ ?>
                            <tr>
                                <td><?php echo $language['name']; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>