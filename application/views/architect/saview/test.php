<input type="checkbox" name="t_selected" id="t_selected" checked data-on-text="Yes" data-off-text="No">

<form>
    <label for="flip-1">Flip toggle switch:</label>
    <select name="flip-1" id="flip-1" data-role="slider">
        <option value="off">Off</option>
        <option value="on">On</option>
    </select>
</form>

<script>

    $(document).ready(function () {
        $("#t_selected").bootstrapSwitch();
    });
</script>
