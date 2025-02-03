<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import Button from '../base/Button.vue';
import axios from 'axios';

const props = defineProps({
    task: Object,
    currentStatus: String,
});
const emit = defineEmits(['statusUpdated']);

const dialog = ref(false);
const isLoading = ref(false);
const statusOptions = [
    { title: 'In-Progress', value: 'in-progress' },
    { title: 'Done', value: 'done' },
];
const selectedStatus = ref(props.currentStatus);

const handleStatusUpdate = async () => {
    if (!selectedStatus.value) {
        console.error('Please select a status.');
        return;
    }

    isLoading.value = true;
    try {
        const userAssigned = Array.isArray(props.task.user_assigned)
            ? props.task.user_assigned
            : JSON.parse(props.task.user_assigned)

        const { data } = await axios.put(`/task/${props.task.id}`, {
            title: props.task.title,
            description: props.task.description,
            status: selectedStatus.value,
            user_assigned: userAssigned
        });
        emit('statusUpdated', data);
        dialog.value = false;
    } catch (error) {
        console.error('Error updating task status:', error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <v-dialog v-model="dialog" max-width="400">
        <template v-slot:activator="{ props: activatorProps }">
            <v-btn class="text-none font-weight-regular" prepend-icon="mdi-arrow-right" text="Move Task" variant="tonal"
                color="green" v-bind="activatorProps" />
        </template>

        <v-card>
            <v-card-title class="headline">Move Task Status</v-card-title>
            <v-card-text>
                <v-select v-model="selectedStatus" :items="statusOptions" :item-title="title" :item-value="value"
                    label="Select New Status" variant="outlined" />
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <Button color="red" text="Cancel" variant="plain" @click="dialog = false" />
                <Button color="green" text="Move Task" variant="tonal" :loading="isLoading" :disable="isLoading"
                    @click="handleStatusUpdate" />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
