
<div class="wrap">
    <h1>FWT Settings</h1>
    <table class="form-table">
        <tbody>
        <tr>
            <th>
                API KEY
            </th>

            <td>
                <form method="POST" action="?page=fwt-settings&amp;action=add_key">
                    <input type="text" style="width: 350px" name="api_key">
                    <button class="button button-primary">Save</button>
                </form>
            </td>
        </tr>
        <tr>
            <th>
                Secutiry key
            </th>

            <td>
                <form method="POST" action="?page=fwt-settings&amp;action=add_secret_key">
                    <input type="text" style="width: 350px" name="secret_key">
                    <button class="button button-primary">Save</button>
                </form>
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