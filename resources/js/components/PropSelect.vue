<template>
    <div class="form-group">
        <div class="row">
            
            <div class="col-md-4 mb-2">
                <label  class="form-control-label">Danh mục</label>
                <select class="form-control" v-model="category" required>
                    <template v-for="(provider, i) in groups">
                        <optgroup :label="provider.title">
                            <template v-for="(item, j) in provider.childs">
                                <option :value="item" v-if="item.display_type == type || type == null">{{ item.title }}</option>
                            </template>
                        </optgroup>
                    </template>
                </select>
            </div>
        </div>
        <template v-if="category">
            <template v-if="catonly">
                Chưa có file mẫu? 
                <a :href="'/assets/backend/files/acc-auto-'+category.position+'.xlsx?t='+time" class="btn btn-sm btn-success">Tải file mẫu</a>
            </template>
            <template v-else v-for="(skill, i) in category.childs">
                <label class="form-control-label">{{ skill.title }}:</label>
                <div class="mb-2" v-if="skill.position == 'radio'">
                    <template v-for="(item, j) in skill.childs">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="radio" :name="'radio_'+skill.id" :id="'label-prop-'+item.id" :value="item.id" class="custom-control-input" :checked="label.indexOf(item.id) > -1">
                            <label class="custom-control-label" :for="'label-prop-'+item.id">{{ item.title }}</label>
                            <input type="hidden" name="groups[]" :value="item.id" v-if="label.indexOf(item.id) > -1">
                        </div>
                    </template>
                </div>
                <div class="mb-2" v-else-if="skill.position == 'checkbox'">
                    <template v-for="(item, j) in skill.childs">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" name="groups[]" :id="'label-prop-'+item.id" :value="item.id" class="custom-control-input" :checked="label.indexOf(item.id) > -1">
                            <label class="custom-control-label" :for="'label-prop-'+item.id">{{ item.title }}</label>
                        </div>
                    </template>
                </div>
                <div class="mb-2 row" v-else-if="skill.position == 'select'">
                    <div class="col-md-4">
                        <select class="form-control" name="groups[]">
                            <template v-for="(item, j) in skill.childs">
                                <option :value="item.id" :selected="label.indexOf(item.id) > -1">{{ item.title }}</option>
                            </template>
                        </select>
                    </div>
                </div> 
                <div class="mb-2 row" v-else-if="skill.position == 'text'">
                    <template v-for="(item, j) in skill.childs">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">{{ item.title }}</label>
                                <input type="text" :name="'params[ext_info]['+item.id+']'" class="form-control" v-model="meta_props[item.id]">
                            </div>
                        </div>
                    </template>
                </div>  
            </template>
            <input type="hidden" name="groups[]" :value="category.parent_id">
            <input type="hidden" name="groups[]" :value="category.id">
            <input type="hidden" name="parent_id" :value="category.id">
        </template>
        <div class="alert alert-danger" id="has-required" v-if="!category">
            Vui lòng chọn các trường bên trên
        </div>
    </div>
</template>

<script>
    export default {
        props: ['groups', 'config', 'ids', 'extend', 'catonly', 'time'],
        data() {
            return {
                type: null,
                provider: null,
                category: null,
                label: [],
                meta_props: {},
            };
        },
        watch: {
            // category: function(){
            //     this.type = this.category.display_type;
            // }
        },
        mounted() {
            this.meta_props = this.extend;
            for (var i = 0; i < this.groups.length; i++) {
                for (var j = 0; j < this.groups[i].childs.length; j++) {
                    if (this.ids.indexOf(this.groups[i].childs[j].id) > -1) {
                        this.provider = this.groups[i];
                        this.category = this.groups[i].childs[j];
                        this.type = this.groups[i].childs[j].display_type;
                    }
                    for (var k = 0; k < this.groups[i].childs[j].childs.length; k++) {
                        for (var n = 0; n < this.groups[i].childs[j].childs[k].childs.length; n++) {
                            var id = this.groups[i].childs[j].childs[k].childs[n].id;
                            if (this.ids.indexOf(id) > -1) {
                                this.label.push(id);
                            }
                            this.$set(this.meta_props, id, this.extend[id]? this.extend[id]: '');
                        }
                    }
                }
            }
        }
    }
</script>
