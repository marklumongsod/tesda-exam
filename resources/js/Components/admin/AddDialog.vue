<script setup lang="ts">
import { ref, defineEmits } from 'vue';
import Button from '../base/Button.vue';
import TextField from '../base/TextField.vue';
import { FormData } from '../../../../types/formData'
import axios from 'axios';

const props = defineProps({ users: Object });
const dialog = ref(false);
const form = ref<FormData>({
    title: '',
    description: '',
    user_assigned: []
});

const emit = defineEmits(['created']);

const handleSubmit = async () => {
    try {
        const { data } = await axios.post('/task', form.value);
        emit('created', data);
        dialog.value = false;
        form.value = { title: '', description: '', user_assigned: [] };
    } catch (error) {
        console.error('Error adding task:', error);
    }
};
</script>

<template>
    <v-dialog v-model="dialog" max-width="600">
        <template v-slot:activator="{ props: activatorProps }">
            <v-btn class="text-none font-weight-regular" prepend-icon="mdi-notebook-plus" text="Add Task"
                variant="tonal" color="orange" v-bind="activatorProps"></v-btn>
        </template>

        <v-card prepend-icon="mdi-pencil" title="Add Task">
            <v-card-text>
                <v-row dense>
                    <v-col cols="12" md="6" sm="6">
                        <TextField label="Title*" required v-model="form.title"></TextField>
                    </v-col>

                    <v-col cols="12" md="6" sm="6">
                        <TextField v-model="form.description" label="Description">
                        </TextField>
                    </v-col>

                </v-row>
                <v-row dense>
                    <v-col cols="12" md="12" sm="12">
                        <v-select v-model="form.user_assigned" :items="props.users" item-title="name" item-value="id"
                            label="Task assign" variant="outlined" auto-select-first multiple></v-select>
                    </v-col>
                </v-row>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <Button color="red" text="Close" variant="plain" @click="dialog = false" />
                <Button color="blue" text="Add" variant="tonal" @click="handleSubmit" />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
