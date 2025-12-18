<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const token = ref(localStorage.getItem('token') || '');
const email = ref('');
const password = ref('');
const registerName = ref('');
const registerEmail = ref('');
const registerPassword = ref('');
const registerRole = ref('student');
const courses = ref([]);
const loading = ref(false);
const errorMsg = ref('');
const instructors = ref([]);

if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
}

const canFetch = computed(() => !!token.value);

const setToken = (value) => {
    token.value = value;
    localStorage.setItem('token', value);
    axios.defaults.headers.common['Authorization'] = `Bearer ${value}`;
};

const login = async () => {
    errorMsg.value = '';
    try {
        const { data } = await axios.post('/api/login', {
            email: email.value,
            password: password.value,
        });
        setToken(data.access_token);
        await fetchAll();
    } catch (e) {
        errorMsg.value = 'Login inválido';
    }
};

const register = async () => {
    errorMsg.value = '';
    try {
        const { data } = await axios.post('/api/register', {
            name: registerName.value,
            email: registerEmail.value,
            password: registerPassword.value,
            role: registerRole.value,
        });
        if (data.access_token) {
            setToken(data.access_token);
        }
        await fetchAll();
    } catch (e) {
        errorMsg.value = 'Registro inválido';
    }
};

const logout = () => {
    token.value = '';
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
    courses.value = [];
};

const fetchCourses = async () => {
    const { data } = await axios.get('/api/courses');
    courses.value = data.data;
};

const fetchInstructors = async () => {
    const { data } = await axios.get('/api/instructors');
    instructors.value = data.data;
};

const fetchAll = async () => {
    if (!canFetch.value) return;
    loading.value = true;
    try {
        await Promise.all([fetchCourses(), fetchInstructors()]);
    } finally {
        loading.value = false;
    }
};

const favorite = async (courseId, isFavorite) => {
    if (!canFetch.value) return;
    if (isFavorite) {
        await axios.delete(`/api/courses/${courseId}/favorite`);
    } else {
        await axios.post(`/api/courses/${courseId}/favorite`);
    }
    await fetchCourses();
};

const newComments = ref({});
const addComment = async (courseId) => {
    if (!canFetch.value) return;
    const current = newComments.value[courseId] || {};
    const payload = {
        text: current.text ?? '',
        rating: Number(current.rating ?? 5) || 5,
    };
    await axios.post(`/api/courses/${courseId}/comments`, payload);
    newComments.value[courseId] = { text: '', rating: 5 };
    await fetchCourses();
};

// Crear curso (selecciona instructor)
const newCourse = ref({
    instructor_id: '',
    title: '',
    description: '',
    lessons: [{ title: '', video_url: '', order: 1 }],
});

const addLesson = () => {
    newCourse.value.lessons.push({
        title: '',
        video_url: '',
        order: newCourse.value.lessons.length + 1,
    });
};

const createCourse = async () => {
    if (!canFetch.value) return;
    errorMsg.value = '';
    try {
        await axios.post('/api/courses', {
            instructor_id: newCourse.value.instructor_id,
            title: newCourse.value.title,
            description: newCourse.value.description,
            lessons: newCourse.value.lessons.filter((l) => l.title && l.video_url),
        });
        newCourse.value = {
            instructor_id: '',
            title: '',
            description: '',
            lessons: [{ title: '', video_url: '', order: 1 }],
        };
        await fetchAll();
    } catch (e) {
        errorMsg.value = 'Error al crear curso';
    }
};

onMounted(fetchAll);
</script>

<template>
  <div class="p-6 max-w-5xl mx-auto font-sans space-y-6">
    <header class="flex flex-col gap-3 md:flex-row md:items-center">
      <h1 class="text-2xl font-semibold">Cursos</h1>
      <div class="md:ml-auto flex flex-col gap-2 md:flex-row md:items-center">
        <template v-if="!token">
          <div class="flex flex-wrap gap-2 items-center">
            <input v-model="email" placeholder="email" class="border px-2 py-1 text-sm" />
            <input v-model="password" type="password" placeholder="password" class="border px-2 py-1 text-sm" />
            <button @click="login" class="bg-black text-white px-3 py-1 text-sm">Login</button>
          </div>
          <div class="flex flex-wrap gap-2 items-center">
            <input v-model="registerName" placeholder="nombre" class="border px-2 py-1 text-sm" />
            <input v-model="registerEmail" placeholder="email" class="border px-2 py-1 text-sm" />
            <input v-model="registerPassword" type="password" placeholder="password" class="border px-2 py-1 text-sm" />
            <select v-model="registerRole" class="border px-2 py-1 text-sm">
              <option value="student">Student</option>
              <option value="instructor">Instructor</option>
            </select>
            <button @click="register" class="border px-3 py-1 text-sm">Registro</button>
          </div>
        </template>
        <template v-else>
          <span class="text-sm text-gray-600">Autenticado</span>
          <button @click="logout" class="border px-3 py-1 text-sm">Logout</button>
        </template>
      </div>
    </header>

    <p v-if="errorMsg" class="text-red-600 text-sm">{{ errorMsg }}</p>
    <p v-if="loading">Cargando...</p>

    <section v-if="token" class="border rounded p-3 space-y-2">
      <h2 class="font-semibold text-lg">Crear curso</h2>
      <div class="grid gap-2 text-sm">
        <select v-model="newCourse.instructor_id" class="border px-2 py-1">
          <option value="">Selecciona instructor</option>
          <option v-for="inst in instructors" :key="inst.id" :value="inst.id">
            {{ inst.name }}
          </option>
        </select>
        <input v-model="newCourse.title" class="border px-2 py-1" placeholder="Título" />
        <textarea v-model="newCourse.description" class="border px-2 py-1" rows="2" placeholder="Descripción"></textarea>
        <div class="space-y-2">
          <div class="flex justify-between items-center">
            <h3 class="font-medium text-sm">Lecciones</h3>
            <button class="text-blue-600 text-xs underline" @click="addLesson">Añadir lección</button>
          </div>
          <div v-for="(lesson, idx) in newCourse.lessons" :key="idx" class="grid md:grid-cols-3 gap-2">
            <input v-model="lesson.title" class="border px-2 py-1" placeholder="Título lección" />
            <input v-model="lesson.video_url" class="border px-2 py-1" placeholder="Video URL" />
            <input v-model.number="lesson.order" type="number" min="1" class="border px-2 py-1" placeholder="Orden" />
          </div>
        </div>
        <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm w-fit" @click="createCourse">Crear curso</button>
      </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <article v-for="course in courses" :key="course.id" class="border rounded p-3 space-y-2">
        <header class="flex justify-between items-center">
          <div>
            <h2 class="font-semibold">{{ course.title }}</h2>
            <p class="text-sm text-gray-600">{{ course.description }}</p>
            <p class="text-sm text-gray-700">
              Instructor: {{ course.instructor?.name ?? 'N/D' }}
            </p>
          </div>
          <div class="text-right text-sm">
            <div>Rating: {{ course.average_rating }} ({{ course.ratings_count }})</div>
            <button
              class="text-blue-600 underline text-xs"
              @click="favorite(course.id, course.is_favorite)"
              :disabled="!token"
            >
              {{ course.is_favorite ? 'Quitar favorito' : 'Favorito' }}
            </button>
          </div>
        </header>

        <div>
          <h3 class="font-medium text-sm mb-1">Lecciones</h3>
          <ul class="text-sm list-disc pl-4 space-y-1">
            <li v-for="lesson in course.lessons" :key="lesson.id">
              {{ lesson.order }}. {{ lesson.title }} — {{ lesson.video_url }}
            </li>
            <li v-if="!course.lessons || !course.lessons.length" class="text-gray-500">Sin lecciones</li>
          </ul>
        </div>

        <div class="border-t pt-2">
          <h3 class="font-medium text-sm mb-1">Nuevo comentario</h3>
          <div class="flex flex-col gap-2 text-sm">
            <textarea
              class="border p-2"
              rows="2"
              placeholder="Comentario"
              v-model="(newComments[course.id] ??= { text: '', rating: 5 }).text"
            />
            <input
              class="border p-1"
              type="number"
              min="1"
              max="5"
              step="0.1"
              v-model.number="(newComments[course.id] ??= { text: '', rating: 5 }).rating"
              placeholder="Rating 1-5"
            />
            <button
              class="bg-blue-600 text-white px-3 py-1 rounded text-sm"
              :disabled="!token"
              @click="addComment(course.id)"
            >
              Enviar
            </button>
          </div>
        </div>
      </article>
    </section>

    <section>
      <h2 class="font-semibold text-lg mb-2">Instructores</h2>
      <ul class="text-sm list-disc pl-4 space-y-1">
        <li v-for="inst in instructors" :key="inst.id">{{ inst.name }}</li>
        <li v-if="!instructors.length" class="text-gray-500">Sin instructores</li>
      </ul>
    </section>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; }
</style>

