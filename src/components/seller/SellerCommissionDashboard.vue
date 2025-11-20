<template>
  <div class="seller-commission-dashboard">
    <div class="commission-header">
      <h2>💰 Komisyon Özeti</h2>
      <div class="header-actions">
        <select v-model="selectedYear" @change="loadYearlyData" class="year-selector">
          <option v-for="year in availableYears" :key="year" :value="year">
            {{ year }}
          </option>
        </select>
      </div>
    </div>

    <!-- Current Month Summary -->
    <div v-if="currentMonthSummary" class="current-month-card">
      <div class="card-header">
        <h3>Bu Ay ({{ currentMonth }})</h3>
        <span class="status-badge" :class="currentMonthSummary.status">
          {{ getStatusText(currentMonthSummary.status) }}
        </span>
      </div>
      
      <div class="commission-stats">
        <div class="stat-item">
          <span class="stat-label">Toplam Satış</span>
          <span class="stat-value">₺{{ formatNumber(currentMonthSummary.total_sales) }}</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Komisyon Oranı</span>
          <span class="stat-value">%{{ currentMonthSummary.commission_rate }}</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Brüt Komisyon</span>
          <span class="stat-value">₺{{ formatNumber(currentMonthSummary.gross_commission) }}</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Abonelik Ücreti</span>
          <span class="stat-value deducted">-₺{{ formatNumber(currentMonthSummary.subscription_fee) }}</span>
        </div>
        <div class="stat-item highlight">
          <span class="stat-label">Net Kazanç</span>
          <span class="stat-value" :class="currentMonthSummary.net_commission >= 0 ? 'positive' : 'negative'">
            ₺{{ formatNumber(currentMonthSummary.net_commission) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Forecast -->
    <div v-if="forecast" class="forecast-card">
      <h3>📈 Ay Sonu Tahmini</h3>
      <div class="forecast-content">
        <div class="forecast-item">
          <span class="label">Geçen {{ forecast.days_elapsed }} gün:</span>
          <span class="value">₺{{ formatNumber(forecast.current_sales) }}</span>
        </div>
        <div class="forecast-item">
          <span class="label">Günlük ortalama:</span>
          <span class="value">₺{{ formatNumber(forecast.daily_average) }}</span>
        </div>
        <div class="forecast-item highlight">
          <span class="label">Tahmini toplam satış:</span>
          <span class="value">₺{{ formatNumber(forecast.projected_sales) }}</span>
        </div>
        <div class="forecast-item highlight">
          <span class="label">Tahmini net kazanç:</span>
          <span class="value" :class="forecast.projected_net >= 0 ? 'positive' : 'negative'">
            ₺{{ formatNumber(forecast.projected_net) }}
          </span>
        </div>
      </div>
      <div class="forecast-progress">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: (forecast.days_elapsed / forecast.days_elapsed + forecast.days_remaining) * 100 + '%' }"></div>
        </div>
        <span class="progress-text">{{ forecast.days_remaining }} gün kaldı</span>
      </div>
    </div>

    <!-- Yearly Summary -->
    <div v-if="yearlySummary" class="yearly-summary">
      <h3>{{ selectedYear }} Yıllık Özet</h3>
      <div class="yearly-stats">
        <div class="yearly-stat">
          <span class="label">Toplam Satış</span>
          <span class="value">₺{{ formatNumber(yearlySummary.total_sales) }}</span>
        </div>
        <div class="yearly-stat">
          <span class="label">Toplam Brüt Komisyon</span>
          <span class="value">₺{{ formatNumber(yearlySummary.total_gross_commission) }}</span>
        </div>
        <div class="yearly-stat">
          <span class="label">Toplam Abonelik Ücreti</span>
          <span class="value deducted">-₺{{ formatNumber(yearlySummary.total_subscription_fees) }}</span>
        </div>
        <div class="yearly-stat highlight">
          <span class="label">Net Kazanç</span>
          <span class="value positive">₺{{ formatNumber(yearlySummary.total_net_commission) }}</span>
        </div>
      </div>
    </div>

    <!-- Monthly Breakdown -->
    <div v-if="yearlySummary && yearlySummary.monthly_breakdown" class="monthly-breakdown">
      <h3>Aylık Detay</h3>
      <table class="breakdown-table">
        <thead>
          <tr>
            <th>Ay</th>
            <th>Satış</th>
            <th>Komisyon</th>
            <th>Net</th>
            <th>Durum</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="month in yearlySummary.monthly_breakdown" :key="month.month">
            <td>{{ formatMonth(month.month) }}</td>
            <td>₺{{ formatNumber(month.sales) }}</td>
            <td>₺{{ formatNumber(month.commission) }}</td>
            <td :class="month.net >= 0 ? 'positive' : 'negative'">
              ₺{{ formatNumber(month.net) }}
            </td>
            <td>
              <span class="status-badge" :class="month.status">
                {{ getStatusText(month.status) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Plan Comparison -->
    <div v-if="planComparison" class="plan-comparison">
      <h3>🔍 Plan Karşılaştırması</h3>
      <p class="comparison-intro">
        Aylık {{ formatNumber(estimatedSales) }} ₺ satış için en uygun plan:
        <strong>{{ planComparison.recommended.recommended_plan }}</strong>
      </p>
      <div class="comparison-slider">
        <label>Tahmini Aylık Satış:</label>
        <input 
          type="range" 
          v-model.number="estimatedSales" 
          min="1000" 
          max="100000" 
          step="1000"
          @change="loadPlanComparison"
        >
        <span>₺{{ formatNumber(estimatedSales) }}</span>
      </div>
      <table class="comparison-table">
        <thead>
          <tr>
            <th>Plan</th>
            <th>Aylık Ücret</th>
            <th>Komisyon</th>
            <th>Brüt Komisyon</th>
            <th>Net Kazanç</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="plan in planComparison.all_plans" :key="plan.plan" :class="{ recommended: plan.plan === planComparison.recommended.recommended_plan }">
            <td>
              {{ plan.plan }}
              <span v-if="plan.plan === planComparison.recommended.recommended_plan" class="recommended-badge">✓ Önerilen</span>
            </td>
            <td>₺{{ formatNumber(plan.monthly_fee) }}</td>
            <td>%{{ plan.commission_rate }}</td>
            <td>₺{{ formatNumber(plan.gross_commission) }}</td>
            <td :class="plan.net_commission >= 0 ? 'positive' : 'negative'">
              ₺{{ formatNumber(plan.net_commission) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

export default {
  name: 'SellerCommissionDashboard',
  setup() {
    const currentMonthSummary = ref(null);
    const forecast = ref(null);
    const yearlySummary = ref(null);
    const planComparison = ref(null);
    const selectedYear = ref(new Date().getFullYear());
    const estimatedSales = ref(10000);
    const loading = ref(false);

    const currentMonth = computed(() => {
      const now = new Date();
      return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
    });

    const availableYears = computed(() => {
      const currentYear = new Date().getFullYear();
      return [currentYear, currentYear - 1, currentYear - 2];
    });

    const loadCurrentMonth = async () => {
      try {
        const response = await axios.get('/api/seller/commissions/current');
        currentMonthSummary.value = response.data;
      } catch (error) {
        console.error('Error loading current month:', error);
      }
    };

    const loadForecast = async () => {
      try {
        const response = await axios.get('/api/seller/commissions/forecast');
        forecast.value = response.data;
      } catch (error) {
        console.error('Error loading forecast:', error);
      }
    };

    const loadYearlyData = async () => {
      try {
        const response = await axios.get(`/api/seller/commissions?year=${selectedYear.value}`);
        yearlySummary.value = response.data;
      } catch (error) {
        console.error('Error loading yearly data:', error);
      }
    };

    const loadPlanComparison = async () => {
      try {
        const response = await axios.get(`/api/seller/commissions/compare-plans?estimated_sales=${estimatedSales.value}`);
        planComparison.value = response.data;
      } catch (error) {
        console.error('Error loading plan comparison:', error);
      }
    };

    const formatNumber = (value) => {
      if (!value) return '0.00';
      return Number(value).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const formatMonth = (monthStr) => {
      const [year, month] = monthStr.split('-');
      const months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
      return `${months[parseInt(month) - 1]} ${year}`;
    };

    const getStatusText = (status) => {
      const statusMap = {
        pending: 'Beklemede',
        calculated: 'Hesaplandı',
        paid: 'Ödendi'
      };
      return statusMap[status] || status;
    };

    onMounted(() => {
      loadCurrentMonth();
      loadForecast();
      loadYearlyData();
      loadPlanComparison();
    });

    return {
      currentMonthSummary,
      forecast,
      yearlySummary,
      planComparison,
      selectedYear,
      estimatedSales,
      loading,
      currentMonth,
      availableYears,
      loadYearlyData,
      loadPlanComparison,
      formatNumber,
      formatMonth,
      getStatusText,
    };
  },
};
</script>

<style scoped>
.seller-commission-dashboard {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.commission-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.commission-header h2 {
  margin: 0;
  font-size: 28px;
  color: #2c3e50;
}

.year-selector {
  padding: 8px 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.current-month-card, .forecast-card, .yearly-summary, .monthly-breakdown, .plan-comparison {
  background: #fff;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header h3 {
  margin: 0;
  font-size: 20px;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.calculated {
  background: #d1ecf1;
  color: #0c5460;
}

.status-badge.paid {
  background: #d4edda;
  color: #155724;
}

.commission-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.stat-item.highlight {
  background: #f8f9fa;
  padding: 16px;
  border-radius: 8px;
  border-left: 4px solid #28a745;
}

.stat-label {
  font-size: 14px;
  color: #6c757d;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #2c3e50;
}

.stat-value.positive {
  color: #28a745;
}

.stat-value.negative {
  color: #dc3545;
}

.stat-value.deducted {
  color: #dc3545;
}

.forecast-content {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.forecast-item {
  display: flex;
  justify-content: space-between;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 4px;
}

.forecast-item.highlight {
  background: #e7f5ff;
  font-weight: 600;
}

.forecast-progress {
  margin-top: 20px;
}

.progress-bar {
  height: 8px;
  background: #e9ecef;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #28a745, #20c997);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 14px;
  color: #6c757d;
}

.yearly-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.yearly-stat {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.yearly-stat.highlight {
  background: #d4edda;
  border-left: 4px solid #28a745;
}

.breakdown-table, .comparison-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 16px;
}

.breakdown-table th, .comparison-table th {
  background: #f8f9fa;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
}

.breakdown-table td, .comparison-table td {
  padding: 12px;
  border-bottom: 1px solid #dee2e6;
}

.breakdown-table tr:hover, .comparison-table tr:hover {
  background: #f8f9fa;
}

.comparison-table tr.recommended {
  background: #d4edda;
}

.recommended-badge {
  background: #28a745;
  color: white;
  padding: 2px 8px;
  border-radius: 8px;
  font-size: 11px;
  margin-left: 8px;
}

.comparison-slider {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 20px 0;
}

.comparison-slider input[type="range"] {
  flex: 1;
}

.comparison-intro {
  margin-bottom: 16px;
  font-size: 16px;
}

h3 {
  margin: 0 0 16px 0;
  font-size: 18px;
  color: #2c3e50;
}
</style>
