<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        @keydown.esc="cancel"
        v-bind:style="{ zIndex: options.zIndex }"
        >
        <v-form @submit.prevent="tipoDemanda.id != null ? update() : save()">
            <v-card>
                <v-toolbar dark :color="options.color" dense flat>
                           <v-toolbar-title class="white--text">{{ formTitle }}</v-toolbar-title>
                </v-toolbar>

                <v-card-text>
                    <v-container grid-list-md>
                        <tipo-demanda-form ref="tipoDemandaForm" v-model="tipoDemanda"></tipo-demanda-form>
                    </v-container>
                    <ul>
                        <li><small><span class="required">*</span> <b>(Asterisco)</b> Indica os campos que são obrigatórios</small></li>
                    </ul>   
                </v-card-text>
                <v-card-actions class="pt-0">
                    <v-spacer></v-spacer>
                    <v-btn color="green" flat="flat" type="submit">
                        Salvar
                        <v-icon right dark>check</v-icon>
                    </v-btn>

                    <v-btn color="blue" flat="flat" @click.native="clear" :disabled="tipoDemanda.id != null">
                           Limpar
                           <v-icon right dark>delete_sweep</v-icon>
                    </v-btn>
                    <v-btn color="red" flat="flat" @click.native="cancel">
                        Cancelar
                        <v-icon right dark>cancel</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import TipoDemandaForm from "@/components/cadastro/forms/TipoDemandaForm.vue";
    export default {
        components: {
            TipoDemandaForm
        },
        data: () => ({
                tipoDemanda: {},
                dialog: false,
                resolve: null,
                reject: null,
                formTitle: null,
                options: {
                    color: "primary",
                    width: 1000,
                    zIndex: 1000
                }
            }),
        methods: {
            open(title, item, options) {
                this.dialog = true;
                this.formTitle = title;
                this.tipoDemanda = item;
                this.$refs.tipoDemandaForm.$validator.errors.clear();
                this.options = Object.assign(this.options, options);
                return new Promise((resolve, reject) => {
                    this.resolve = resolve;
                    this.reject = reject;
                });
            },
            save() {
                this.$refs.tipoDemandaForm.$validator.validateAll().then(valid => {
                    if (valid) {
                        this.$store
                                .dispatch("newTipoDemanda", this.tipoDemanda)
                                .then(() => {
                                    this.resolve(true);
                                    this.dialog = false;
                                    this.tipoDemanda = {};
                                    window.getApp.$emit("APP_SUCCESS", {msg: 'Dados salvo com sucesso!', timeout: 2000});
                                }).catch((resp) => {
                            this.addErrors(resp);
                        });


                    }
                });
            },
            update() {
                this.$refs.tipoDemandaForm.$validator.validateAll().then(valid => {
                    if (valid) {
                        this.$store
                                .dispatch("updateTipoDemanda", this.tipoDemanda)
                                .then(() => {
                                    this.resolve(true);
                                    this.dialog = false;
                                    this.tipoDemanda = {};
                                    window.getApp.$emit("APP_SUCCESS", {msg: 'Dados atualizados com sucesso!', timeout: 2000});
                                }).catch((resp) => {
                            this.addErrors(resp);
                        });
                    }
                });
            },
            cancel() {
                this.resolve(false);
                this.dialog = false;
            },
            clear() {
                this.tipoDemanda = {};
                this.$refs.tipoDemandaForm.$validator.errors.clear();
            },
            addErrors(resp) {
                window.getApp.$emit("APP_ERROR", {msg: 'Ops! Ocorreu algum erro.', timeout: 2000});
                if (resp.response.data.errors.nome) {
                    this.$refs.tipoDemandaForm.$validator.errors.add({field: 'Nome', msg: resp.response.data.errors.nome});
                }
                if (resp.response.data.errors.descricao) {
                    this.$refs.tipoDemandaForm.$validator.errors.add({field: 'Descrição', msg: resp.response.data.errors.descricao});
                }
            }
        }
    };
</script>