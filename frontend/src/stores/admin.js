import { defineStore } from "pinia";
import { ref } from "vue";
import { getDashboardStatsApi } from "@/services/admin";

export const useAdminStore = defineStore("admin", () => {
  const stats = ref({
    total_users: 0,
    total_jobs: 0,
    pending_jobs: 0,
    approved_jobs: 0,
    rejected_jobs: 0,
  });

  const loading = ref(false);
  const error = ref(null);

  async function fetchDashboardStats() {
    loading.value = true;
    error.value = null;
    try {
      const response = await getDashboardStatsApi();
      stats.value = { ...stats.value, ...response.data };
    } catch (err) {
      console.error("Fetch Admin Stats Error:", err);
      error.value = "Failed to load dashboard statistics.";
    } finally {
      loading.value = false;
    }
  }

  return { stats, loading, error, fetchDashboardStats };
});
