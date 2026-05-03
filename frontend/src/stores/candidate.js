import { defineStore } from "pinia";
import { ref } from "vue";
import {
  getMyApplicationsApi,
  cancelApplicationApi,
} from "@/services/applications";

export const useCandidateStore = defineStore("candidate", () => {
  const applications = ref([]);
  const loading = ref(false);
  const error = ref(null);

  async function fetchMyApplications() {
    loading.value = true;
    error.value = null;
    try {
      const response = await getMyApplicationsApi();
      applications.value = response.data;
    } catch (err) {
      console.error("Fetch Applications Error:", err);
      error.value = "Failed to load your applications.";
    } finally {
      loading.value = false;
    }
  }

  async function cancelApplication(id) {
    try {
      await cancelApplicationApi(id);
      applications.value = applications.value.filter((app) => app.id !== id);
    } catch (err) {
      console.error("Cancel Application Error:", err);
      throw err;
    }
  }

  return {
    applications,
    loading,
    error,
    fetchMyApplications,
    cancelApplication,
  };
});
