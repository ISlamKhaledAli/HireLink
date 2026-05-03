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

  const pendingJobs = ref([]);
  const pagination = ref({});

  const loading = ref(false);
  const loadingJobs = ref(false);
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

  async function fetchPendingJobs(page = 1) {
    loadingJobs.value = true;
    try {
      const response = await getPendingJobsApi(page);
      pendingJobs.value = response.data.data;
      pagination.value = response.data.meta || response.data;
    } catch (err) {
      console.error("Fetch Pending Jobs Error:", err);
      error.value = "Failed to load pending jobs.";
    } finally {
      loadingJobs.value = false;
    }
  }

  async function approveJob(id) {
    try {
      await approveJobApi(id);
      pendingJobs.value = pendingJobs.value.filter((job) => job.id !== id);
      if (stats.value.pending_jobs > 0) stats.value.pending_jobs--;
      stats.value.approved_jobs++;
    } catch (err) {
      console.error("Approve Job Error:", err);
      throw err;
    }
  }

  async function rejectJob(id) {
    try {
      await rejectJobApi(id);
      pendingJobs.value = pendingJobs.value.filter((job) => job.id !== id);
      if (stats.value.pending_jobs > 0) stats.value.pending_jobs--;
      stats.value.rejected_jobs++;
    } catch (err) {
      console.error("Reject Job Error:", err);
      throw err;
    }
  }

  return {
    stats,
    pendingJobs,
    pagination,
    loading,
    loadingJobs,
    error,
    fetchDashboardStats,
    fetchPendingJobs,
    approveJob,
    rejectJob,
  };

});
