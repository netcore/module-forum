<div class="modal fade" tabindex="-1" role="dialog" id="block-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Block or unblock user</h4>
            </div>

            <div class="modal-body">
                <div v-if="blockModal.isLoading" class="text-center">
                    <i class="fa fa-spin fa-spinner fa-3x"></i>
                </div>

                <div v-show="!blockModal.isLoading">
                    <table class="table table-stripped m-b-0">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Expires at</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="entry in blockModal.entries">
                            <td v-text="entry.text"></td>
                            <td v-text="entry.expires_at"></td>
                            <td class="text-right">
                                <button type="button" class="btn btn-xs btn-danger"
                                        @click="removeBlacklistEntry($event, entry)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!blockModal.entries.length">
                            <td colspan="4">
                                <div class="text-danger">No active entries found.</div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <hr class="m-y-1">

                    <!-- Create form -->
                    <div class="form-group">
                        <label class="control-label">Block user in:</label>

                        <label class="custom-control custom-radio">
                            <input type="radio"
                                   name="level"
                                   class="custom-control-input"
                                   value="forum"
                                   :disabled="blockModal.disabled.forum"
                                   v-model="blockModal.form.level">
                            <span class="custom-control-indicator"></span>
                            Entire forum
                        </label>

                        <label class="custom-control custom-radio">
                            <input type="radio"
                                   name="level"
                                   class="custom-control-input"
                                   value="category"
                                   :disabled="blockModal.disabled.forum"
                                   v-model="blockModal.form.level"
                            >
                            <span class="custom-control-indicator"></span>
                            Category
                        </label>

                        <label class="custom-control custom-radio">
                            <input type="radio"
                                   name="level"
                                   class="custom-control-input"
                                   value="thread"
                                   :disabled="blockModal.disabled.thread || blockModal.disabled.forum"
                                   v-model="blockModal.form.level">
                            <span class="custom-control-indicator"></span>
                            Current thread
                        </label>
                    </div>

                    <div class="form-group" v-show="blockModal.form.level === 'category'">
                        <label for="block-category">Select category:</label>
                        <select id="block-category" v-model="blockModal.form.category_id" class="form-control">
                            <option value="" selected>-</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">Expiration date:</span>
                            <input type="date" class="form-control" v-model="blockModal.form.expires_at">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" v-show="!blockModal.isLoading">
                <button type="button" class="btn btn-primary" @click="addBlacklistEntry($event)">
                    <i class="fa fa-plus"></i> Add entry
                </button>
            </div>
        </div>
    </div>
</div>