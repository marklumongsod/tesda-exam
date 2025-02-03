<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AddDialog from '@/Components/admin/AddDialog.vue'
import EditDialog from '@/Components/admin/EditDialog.vue'
import DeleteDialog from '@/Components/admin/DeleteDialog.vue'
import MoveDialog from '@/Components/admin/MoveDialog.vue'
import axios from 'axios';

const headers = [
    { text: 'Title', value: 'title' },
    { text: 'Description', value: 'description' },
    { text: 'Assigned User/s', value: 'user_assigned' },
];

interface Task {
    id: number;
    title: string;
    description: string;
    user_assigned: string[];
}

const tasks = ref<Task[]>([]);
const tasksTodo = ref<Task[]>([]);
const tasksInProgress = ref<Task[]>([]);
const tasksDone = ref<Task[]>([]);
const users = ref([]);
const loading = ref(false);

const fetchTaskTodo = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/task/to-do');
        tasksTodo.value = response.data;
        console.log('tasksTodo', tasksTodo);
    } catch (error) {
        console.error('Error fetching task:', error);
    } finally {
        loading.value = false;
    }
};

const fetchTaskInProgress = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/task/in-progress');
        tasksInProgress.value = response.data;
        console.log('tasksInProgress', tasksInProgress)
    } catch (error) {
        console.error('Error fetching task:', error);
    } finally {
        loading.value = false;
    }
};

const fetchTaskDone = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/task/done');
        tasksDone.value = response.data;
        console.log('tasksDone', tasksDone)
    } catch (error) {
        console.error('Error fetching task:', error);
    } finally {
        loading.value = false;
    }
};

const addTask = (userData: any) => {
    tasks.value.push(userData);
    fetchTaskTodo();
    fetchTaskInProgress();
    fetchTaskDone();
};

const updateTask = (updatedTask: any) => {
    const index = tasks.value.findIndex((task) => task.id === updatedTask.id);
    if (index !== -1) tasks.value[index] = updatedTask;
    fetchTaskTodo();
    fetchTaskInProgress();
    fetchTaskDone();
};

const moveTask = (taskId: number, newStatus: string) => {
    const task = tasks.value.find((task) => task.id === taskId);
    if (task) {
        task.status = newStatus;
        updateTask(task);
    }
    fetchTaskTodo();
    fetchTaskInProgress();
    fetchTaskDone();
};

const deleteTask = (taskId: any) => {
    tasks.value = tasks.value.filter((user) => user.id !== taskId);
    fetchTaskTodo();
    fetchTaskInProgress();
    fetchTaskDone();
};

const fetchUsers = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/user');
        users.value = response.data;
        console.log('users', users.value);
    } catch (error) {
        console.error('Error fetching users:', error);
    } finally {
        loading.value = false;
    }
};

const findUser = (ids: number[]): string => {
    if (!users.value || !Array.isArray(users.value)) {
        return '';
    }
    const foundUsers = users.value.filter((user: { id: number }) => ids.includes(user.id));
    return foundUsers.map((user: { name: string }) => user.name).join(', ');
};

onMounted(async () => {
    await fetchTaskTodo();
    await fetchTaskInProgress();
    await fetchTaskDone();
    await fetchUsers();
});

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-3">
            <div class="mx-auto max-w-8xl sm:px-2 lg:px-1">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <v-container fluid>
                        <v-row>
                            <!-- {{ users }} -->
                            <v-col cols="4">
                                <AddDialog @created="addTask" :users="users" />

                                <v-card title="To-do" class="mt-5" variant="outlined">
                                    <v-table dense>
                                        <thead>
                                            <tr>
                                                <th v-for="header in headers" :key="header.value">{{ header.text }}</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="tasksTodo.length === 0">
                                                <td colspan="4" class="text-center text-gray-500">No data available</td>
                                            </tr>
                                            <tr v-else v-for="task in tasksTodo" :key="task.id">
                                                <td>{{ task.title }}</td>
                                                <td>{{ task.description }}</td>
                                                <td>{{ findUser(task.user_assigned) }}</td>
                                                <td>
                                                    <div class="flex">
                                                        <MoveDialog :task="task" :currentStatus="task.status"
                                                            @statusUpdated="moveTask" />
                                                        <EditDialog :task="task" @updated="updateTask" :assignUsers="findUser(task.user_assigned)" class="space-x-2"
                                                            :users="users" />
                                                        <DeleteDialog :taskId="task.id" @deleted="deleteTask" />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-card>
                            </v-col>
                            <v-col cols="4">
                                <v-card title="In-progress" class="mt-14" variant="outlined">
                                    <v-table dense>
                                        <thead>
                                            <tr>
                                                <th v-for="header in headers" :key="header.value">{{ header.text }}</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="tasksInProgress.length === 0">
                                                <td colspan="4" class="text-center text-gray-500">No data available</td>
                                            </tr>
                                            <tr v-else v-for="task in tasksInProgress" :key="task.id">
                                                <td>{{ task.title }}</td>
                                                <td>{{ task.description }}</td>
                                                <td>{{ findUser(task.user_assigned) }}</td>
                                                <td>
                                                    <div class="flex ">
                                                        <MoveDialog :task="task" :currentStatus="task.status"
                                                            @statusUpdated="moveTask" />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-card>
                            </v-col>
                            <v-col cols="4">
                                <v-card title="Done" class="mt-14" variant="outlined">
                                    <v-table dense>
                                        <thead>
                                            <tr>
                                                <th v-for="header in headers" :key="header.value">{{ header.text }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="tasksDone.length === 0">
                                                <td colspan="4" class="text-center text-gray-500">No data available</td>
                                            </tr>
                                            <tr v-else v-for="task in tasksDone" :key="task.id">
                                                <td>{{ task.title }}</td>
                                                <td>{{ task.description }}</td>
                                                <td>{{ findUser(task.user_assigned) }}</td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-container>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>