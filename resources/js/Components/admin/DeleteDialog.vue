<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import Prompt from '../base/Prompt.vue';
import Button from '../base/Button.vue';
import axios from 'axios';

const props = defineProps({ taskId: Number });
const emit = defineEmits(['deleted']);

const prompt = ref(false);
const isLoading = ref(false);

const handleDelete = async () => {
    isLoading.value = true;
    try {
        await axios.delete(`/task/${props.taskId}`);
        emit('deleted', props.taskId);
        prompt.value = false;
    } catch (error) {
        console.error('Error deleting task:', error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <v-btn class="text-none font-weight-regular" prepend-icon="mdi-delete" text="Delete" variant="tonal"
        @click="prompt = true" color="red"></v-btn>
    <Prompt :title="`Are you sure you want to delete this task?`" v-model="prompt">
        <Button text="Yes, delete it" color="red" size="small" variant="tonal" class="tw-pr-4 tw-pt-4"
            @click="handleDelete" :loading="isLoading" :disable="isLoading" />
    </Prompt>
</template>
