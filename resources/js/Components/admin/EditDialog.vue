<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import Button from '../base/Button.vue';
import TextField from '../base/TextField.vue';
import axios from 'axios';

const props = defineProps({ task: Object, users: Object });
const emit = defineEmits(['updated']);

const dialog = ref(false);
const form = ref({ ...props.task });

const handleUpdate = async () => {
  try {
    if (props.task) {
      const { data } = await axios.put(`/task/${props.task.id}`, form.value);
      emit('updated', data);
    } else {
      console.error('Task data is not available.');
    }
  } catch (error) {
    console.error('Error updating task:', error);
  } finally {
    dialog.value = false;
  }
};

// onMounted(() => {
//   form.value.user_assigned = props.assignUsers
// })


</script>
<template>
  <v-dialog v-model="dialog" max-width="600">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn class="text-none font-weight-regular" prepend-icon="mdi-notebook-minus" text="Edit Task" variant="tonal"
        color="blue" v-bind="activatorProps"></v-btn>
    </template>

    <v-card prepend-icon="mdi-pencil" title="Edit Task">
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
        <Button color="red" text="Cancel" variant="plain" @click="dialog = false" />
        <Button color="blue" text="Save Changes" variant="tonal" @click="handleUpdate" />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
