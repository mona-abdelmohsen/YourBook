<form method="POST" action="{{ route('filament.resources.reports.bulkDelete') }}">
    @csrf

    <div class="form-group">
        <label for="conclusion">Conclusion</label>
        <textarea id="conclusion" name="conclusion" rows="4" class="form-control" placeholder="Write your conclusion..." required></textarea>
    </div>

    <div class="form-group">
        <label>Action</label>
        <div>
            <label><input type="radio" name="action" value="delete" required> Delete</label>
            <label><input type="radio" name="action" value="ignore" required> Ignore</label>
        </div>
    </div>

    <input type="hidden" name="records" value="{{ json_encode($records->pluck('id')->toArray()) }}">

    <div class="mt-3">
        <button type="submit" class="btn btn-danger">Confirm</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>
