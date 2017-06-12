
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
                        <button class="button button-primary">ADD</button>
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
        </tbody>
    </table>
</div>