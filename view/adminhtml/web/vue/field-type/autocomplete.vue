<template>
    <div
        :class="[
            'admin__field field field-title',
            { 'admin__field--required _required': required }
        ]"
    >
        <label class="label admin__field-label">
            <span>
                {{ label }}
            </span>
        </label>
        <div class="admin__field-control control">
            <treeselect
                v-if="isTree"
                v-model="selected"
                :options="optionsTree"
                :placeholder="placeholder"
                :default-expand-level="1"
                :clearable="false"
                :required="required"
            >
                <template #value-label="{ node }">
                    {{ node.raw.full_label }}
                </template>
            </treeselect>

            <v-select
                v-else
                v-model="selected"
                :options="options"
                :placeholder="placeholder"
                :clearable="false"
                :disabled="isDisabled"
                :required="required"
            >
                <template #option="option">
                    {{ option.label }}

                    <template v-if="option.store && option.store.length">
                        <span class="vs__dropdown-option__details">
                            {{ option.store.join(', ') }}
                        </span>
                    </template>
                </template>
            </v-select>
        </div>
    </div>
</template>

<script>
    define(['Vue'], function(Vue) {
        Vue.component('autocomplete', {
            name: 'autocomplete',
            props: {
                label: {
                    type: String,
                    required: true
                },
                description: {
                    type: String,
                    required: true
                },
                options: {
                    type: Array,
                    required: true
                },
                item: {
                    type: Object,
                    required: true
                },
                config: {
                    type: Object,
                    required: true
                },
                itemKey: {
                    type: String,
                    required: true
                },
                itemIdKey: {
                    type: String,
                    default: null
                },
                defaultOptionValue: {
                    type: String,
                    default: 'default'
                },
                isTree: {
                    type: Boolean,
                    default: false
                },
                isDisabled: {
                    type: Boolean,
                    default: false
                },
                required: {
                    type: Boolean,
                    default: false
                }
            },
            computed: {
                selected: {
                    get() {
                        let selectedOption = '',
                            optionValue;
                        const selectedItemId = this.item[this.itemIdKey];

                        if (selectedItemId) {
                            for (var i = 0; i < this.options.length; i++) {
                                optionId = this.options[i].id.toString();
                                if (optionId === selectedItemId) {
                                    selectedOption = this.isTree ? this.options[i].value : this.options[i];
                                }
                            }
                        } else {
                            for (var i = 0; i < this.options.length; i++) {
                                optionValue = this.options[i].value.toString();
                                if (optionValue === this.item[this.itemKey]) {
                                    selectedOption = this.isTree ? this.options[i].value : this.options[i];
                                }
                            }
                        }

                        if (!selectedOption) {
                            selectedOption = this.defaultSelectedOption;
                        }

                        return selectedOption;
                    },
                    set(option) {
                        if (option && typeof option === 'object') {
                            this.$set(this.item, this.itemKey, option.value.toString());
                            this.$set(this.item, this.itemIdKey, option.id.toString());
                            this.$set(this.item, 'title', option.label);
                        }
                        else if (option && typeof option === 'string') {
                            this.$set(this.item, this.itemKey, option);
                            this.$set(this.item, 'title', this.options.find(item => item.value === option)?.label);
                        }
                        else {
                            this.$set(this.item, this.itemKey, this.defaultSelectedOption ? this.defaultSelectedOption.value.toString() : '');
                        }
                    }
                },
                placeholder() {
                    return this.config.translation.pleaseSelect + ' ' + this.label.toLocaleLowerCase();
                },
                optionsTree() {
                    const hashTable = {},
                            optionsTree = [];

                    this.options.forEach(item => hashTable[item.id] = {...item});
                    this.options.forEach(item => {
                        if (item.parent_id && hashTable[item.parent_id]) {
                            hashTable[item.parent_id].children = [
                                ...(hashTable[item.parent_id].children || []),
                                hashTable[item.id]
                            ];
                        } else {
                            optionsTree.push(hashTable[item.id]);
                        }
                    });

                    return optionsTree;
                }
            },
            created() {
                var optionValue;

                for (var i = 0; i < this.options.length; i++) {
                    optionValue = this.options[i].value.toString();
                    if (optionValue === this.defaultOptionValue) {
                        this.defaultSelectedOption = this.options[i];
                    }
                }
            },
            template: template
        });
    });
</script>
