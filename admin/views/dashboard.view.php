<style>
    .fwt_row {
        margin-bottom: 8px;
    }
</style>
<div class="wrap">
    <h1>FWT Settings</h1>
    <form method="POST" action="?page=fwt-settings&amp;action=add_key">
        <div class="fwt_row">
            <label style="font-width: bold; display: block;">API KEY</label>
            <input type="text" style="width: 350px; display: block;" name="api_key">
        </div>
        <div class="fwt_row">
            <label style="font-width: bold; display: block;">SECRET KEY</label>
            <input type="text" style="width: 350px; display: block;" name="secret_key">
        </div>
        <div class="fwt_row">
            <button class="button button-primary">ADD</button>
        </div>


    </form>
</div>