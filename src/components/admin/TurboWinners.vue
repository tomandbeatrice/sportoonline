<template>
  <div class="turbo-winners-panel">
    <div class="panel-header">
      <h2 class="flex items-center gap-2"><IconFlag cls="w-6 h-6 text-gray-700" /> Turbo Mod Kazananları</h2>
      <p>Aylık yarışma kazananlarını görüntüleyin ve ödülleri yönetin</p>
    </div>

    <!-- Month Selector -->
    <div class="month-selector">
      <button @click="previousMonth" class="nav-btn">◀</button>
      <div class="current-month">
        {{ selectedMonthLabel }}
      </div>
      <button @click="nextMonth" class="nav-btn" :disabled="isCurrentMonth">▶</button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Kazananlar yükleniyor...</p>
    </div>

    <!-- Winners Display -->
    <div v-else-if="winners.customers.length > 0 || winners.sellers.length > 0" class="winners-grid">
      <!-- Customer Winners -->
      <div class="winner-category">
          <h3 class="flex items-center gap-2"><IconCart cls="w-5 h-5 text-gray-700" /> En İyi Müşteriler</h3>
        <div class="winner-list">
          <div 
            v-for="winner in winners.customers" 
            :key="winner.id"
            class="winner-card"
            :class="'rank-' + winner.rank"
          >
            <div class="winner-rank">
              <template v-if="winner.rank === 1">
                <IconMedal cls="w-8 h-8 text-yellow-400" />
              </template>
              <template v-else-if="winner.rank === 2">
                <IconMedal cls="w-8 h-8 text-gray-400" />
              </template>
              <template v-else-if="winner.rank === 3">
                <IconMedal cls="w-8 h-8 text-orange-400" />
              </template>
              <template v-else>
                <span class="text-sm font-semibold">#{{ winner.rank }}</span>
              </template>
            </div>
            <div class="winner-info">
              <div class="winner-name">{{ winner.user.name }}</div>
              <div class="winner-email">{{ winner.user.email }}</div>
              <div class="winner-stat">
                <span class="stat-label">Toplam Alışveriş:</span>
                <span class="stat-value">₺{{ formatNumber(winner.total_amount) }}</span>
              </div>
            </div>
            <div class="winner-rewards">
                <div class="reward-item money">
                <span class="reward-icon"><IconMoney cls="w-5 h-5 text-amber-500" /></span>
                <input 
                  type="number" 
                  v-model="winner.reward_money" 
                  @change="updateReward(winner)"
                  class="reward-input"
                  step="100"
                  min="0"
                >
                <span class="reward-label">TL</span>
              </div>
                <div class="reward-item points">
                <span class="reward-icon"><IconStar cls="w-5 h-5 text-yellow-400" /></span>
                <input 
                  type="number" 
                  v-model="winner.reward_points" 
                  @change="updateReward(winner)"
                  class="reward-input"
                  step="100"
                  min="0"
                >
                <span class="reward-label">Puan</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Seller Winners -->
      <div class="winner-category">
        <h3>💼 En İyi Satıcılar</h3>
        <div class="winner-list">
          <div 
            v-for="winner in winners.sellers" 
            :key="winner.id"
            class="winner-card"
            :class="'rank-' + winner.rank"
          >
            <div class="winner-rank">{{ winner.rank_badge }}</div>
            <div class="winner-info">
              <div class="winner-name">{{ winner.user.name }}</div>
              <div class="winner-email">{{ winner.user.email }}</div>
              <div class="winner-stat">
                <span class="stat-label">Toplam Satış:</span>
                <span class="stat-value">₺{{ formatNumber(winner.total_amount) }}</span>
              </div>
            </div>
            <div class="winner-rewards">
              <div class="reward-item money">
                <span class="reward-icon">💰</span>
                <input 
                  type="number" 
                  v-model="winner.reward_money" 
                  @change="updateReward(winner)"
                  class="reward-input"
                  step="100"
                  min="0"
                >
                <span class="reward-label">TL</span>
              </div>
              <div class="reward-item points">
                <span class="reward-icon"><IconStar cls="w-5 h-5 text-yellow-400" :filled="true" /></span>
                <input 
                  type="number" 
                  v-model="winner.reward_points" 
                  @change="updateReward(winner)"
                  class="reward-input"
                  step="100"
                  min="0"
                >
                <span class="reward-label">Puan</span>
              </div>
                <div class="reward-item coupon" v-if="winner.coupon">
                <span class="reward-icon"><IconTicket cls="w-5 h-5 text-indigo-500" /></span>
                <div class="coupon-info">
                  <div class="coupon-code">{{ winner.coupon.code }}</div>
                  <div class="coupon-discount">%{{ winner.coupon.commission_discount_percent }} İndirim</div>
                  <button @click="copyCoupon(winner.coupon.code)" class="copy-btn">Kopyala</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- No Winners -->
    <div v-else class="no-winners">
      <div class="no-winners-icon">
        <IconFlag cls="w-10 h-10 text-gray-400" />
      </div>
      <p>Bu ay için henüz kazanan belirlenmedi.</p>
      <p class="hint">Yarışma sonunda otomatik olarak kazananlar belirlenir.</p>
    </div>

    <!-- Competition History -->
    <div class="history-section" v-if="!loading">
      <h3 class="flex items-center gap-2"><IconClipboard cls="w-5 h-5 text-gray-700" /> Yarışma Geçmişi</h3>
      <div class="history-stats">
        <div class="stat-card">
          <div class="stat-icon"><IconTrophy cls="w-6 h-6 text-amber-400" /></div>
          <div class="stat-content">
            <div class="stat-value">{{ totalCompetitions }}</div>
            <div class="stat-label">Tamamlanan Yarışma</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><IconMoney cls="w-6 h-6 text-amber-500" /></div>
          <div class="stat-content">
            <div class="stat-value">₺{{ formatNumber(totalRewardsDistributed) }}</div>
            <div class="stat-label">Dağıtılan Ödül</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><IconStar cls="w-6 h-6 text-yellow-400" /></div>
          <div class="stat-content">
            <div class="stat-value">{{ formatNumber(totalPointsDistributed) }}</div>
            <div class="stat-label">Dağıtılan Puan</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><IconTicket cls="w-6 h-6 text-indigo-500" /></div>
          <div class="stat-content">
            <div class="stat-value">{{ activeCoupons }}</div>
            <div class="stat-label">Aktif Kupon</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import IconFlag from '../icons/IconFlag.vue';
import IconCart from '../icons/IconCart.vue';
import IconMedal from '../icons/IconMedal.vue';
import IconMoney from '../icons/IconMoney.vue';
import IconStar from '../icons/IconStar.vue';
import IconTicket from '../icons/IconTicket.vue';
import IconTrophy from '../icons/IconTrophy.vue';
import IconClipboard from '../icons/IconClipboard.vue';

export default {
  name: 'TurboWinners',
  components: { IconFlag, IconCart, IconMedal, IconMoney, IconStar, IconTicket, IconTrophy, IconClipboard },
  setup() {
    const loading = ref(true);
    const selectedYear = ref(new Date().getFullYear());
    const selectedMonth = ref(new Date().getMonth() + 1);
    
    const winners = reactive({
      customers: [],
      sellers: []
    });

    const stats = reactive({
      totalCompetitions: 0,
      totalRewardsDistributed: 0,
      totalPointsDistributed: 0,
      activeCoupons: 0
    });

    const selectedMonthLabel = computed(() => {
      const months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                      'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
      return `${months[selectedMonth.value - 1]} ${selectedYear.value}`;
    });

    const isCurrentMonth = computed(() => {
      const now = new Date();
      return selectedYear.value === now.getFullYear() && 
             selectedMonth.value === now.getMonth() + 1;
    });

    const totalCompetitions = computed(() => stats.totalCompetitions);
    const totalRewardsDistributed = computed(() => stats.totalRewardsDistributed);
    const totalPointsDistributed = computed(() => stats.totalPointsDistributed);
    const activeCoupons = computed(() => stats.activeCoupons);

    const fetchWinners = async () => {
      try {
        loading.value = true;

        const month = `${selectedYear.value}-${String(selectedMonth.value).padStart(2, '0')}`;
        
        const response = await axios.get('/api/admin/turbo-winners/by-month', {
          params: { month },
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        if (response.data) {
          winners.customers = response.data.customers || [];
          winners.sellers = response.data.sellers || [];
        }

        // Fetch overall stats
        const historyResponse = await axios.get('/api/turbo/history', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        if (historyResponse.data.success) {
          const history = historyResponse.data.data;
          
          // Calculate stats
          stats.totalCompetitions = history.length;
          stats.totalRewardsDistributed = history.reduce((sum, comp) => {
            return sum + comp.winners.reduce((s, w) => s + parseFloat(w.reward_money || 0), 0);
          }, 0);
          stats.totalPointsDistributed = history.reduce((sum, comp) => {
            return sum + comp.winners.reduce((s, w) => s + parseInt(w.reward_points || 0), 0);
          }, 0);
          stats.activeCoupons = await getActiveCouponsCount();
        }
      } catch (error) {
        console.error('Kazananlar yüklenemedi:', error);
        winners.customers = [];
        winners.sellers = [];
      } finally {
        loading.value = false;
      }
    };

    const getActiveCouponsCount = async () => {
      try {
        const response = await axios.get('/api/turbo/current', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        return response.data.data?.total_active_coupons || 0;
      } catch {
        return 0;
      }
    };

    const previousMonth = () => {
      if (selectedMonth.value === 1) {
        selectedMonth.value = 12;
        selectedYear.value--;
      } else {
        selectedMonth.value--;
      }
      fetchWinners();
    };

    const nextMonth = () => {
      if (isCurrentMonth.value) return;
      
      if (selectedMonth.value === 12) {
        selectedMonth.value = 1;
        selectedYear.value++;
      } else {
        selectedMonth.value++;
      }
      fetchWinners();
    };

    const updateReward = async (winner) => {
      try {
        // API call to update winner rewards
        await axios.put(`/api/admin/turbo-winners/${winner.id}`, {
          reward_money: winner.reward_money,
          reward_points: winner.reward_points
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        
        console.log('Ödül güncellendi:', winner.id);
      } catch (error) {
        console.error('Ödül güncellenemedi:', error);
        alert('Ödül güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
      }
    };
        //   reward_points: winner.reward_points
        // });
      } catch (error) {
        console.error('Ödül güncellenemedi:', error);
      }
    };

    const copyCoupon = (code) => {
      navigator.clipboard.writeText(code).then(() => {
        alert(`Kupon kodu kopyalandı: ${code}`);
      }).catch(() => {
        alert('Kopyalama başarısız.');
      });
    };

    const formatNumber = (num) => {
      if (!num) return '0';
      return parseFloat(num).toLocaleString('tr-TR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    };

    onMounted(() => {
      fetchWinners();
    });

    return {
      loading,
      selectedMonthLabel,
      isCurrentMonth,
      winners,
      totalCompetitions,
      totalRewardsDistributed,
      totalPointsDistributed,
      activeCoupons,
      previousMonth,
      nextMonth,
      updateReward,
      copyCoupon,
      formatNumber
    };
  }
};
</script>

<style scoped>
.turbo-winners-panel {
  padding: 20px;
}

.panel-header {
  margin-bottom: 30px;
}

.panel-header h2 {
  margin: 0 0 10px 0;
  font-size: 28px;
  color: #1f2937;
}

.panel-header p {
  margin: 0;
  color: #6b7280;
}

.month-selector {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin-bottom: 30px;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
}

.nav-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  font-size: 24px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s;
}

.nav-btn:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

.nav-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.current-month {
  font-size: 24px;
  font-weight: 700;
  color: white;
  min-width: 200px;
  text-align: center;
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e5e7eb;
  border-top-color: #667eea;
  border-radius: 50%;
  margin: 0 auto 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.winners-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
  gap: 30px;
  margin-bottom: 40px;
}

.winner-category h3 {
  margin: 0 0 20px 0;
  font-size: 22px;
  color: #1f2937;
}

.winner-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.winner-card {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 15px;
  padding: 20px;
  display: flex;
  gap: 20px;
  transition: all 0.3s;
}

.winner-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.winner-card.rank-1 {
  border-color: #ffd200;
  background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
}

.winner-card.rank-2 {
  border-color: #c0c0c0;
  background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
}

.winner-card.rank-3 {
  border-color: #cd7f32;
  background: linear-gradient(135deg, #fff5eb 0%, #ffffff 100%);
}

.winner-rank {
  font-size: 48px;
  line-height: 1;
}

.winner-info {
  flex: 1;
}

.winner-name {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 5px;
}

.winner-email {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 10px;
}

.winner-stat {
  display: flex;
  gap: 10px;
  align-items: baseline;
}

.stat-label {
  color: #6b7280;
  font-size: 14px;
}

.stat-value {
  color: #10b981;
  font-weight: 700;
  font-size: 18px;
}

.winner-rewards {
  display: flex;
  flex-direction: column;
  gap: 10px;
  min-width: 200px;
}

.reward-item {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f9fafb;
  padding: 10px;
  border-radius: 8px;
}

.reward-icon {
  font-size: 20px;
}

.reward-input {
  flex: 1;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 14px;
  font-weight: 600;
  text-align: right;
}

.reward-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.reward-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 600;
}

.coupon-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.coupon-code {
  font-family: 'Courier New', monospace;
  font-weight: 700;
  color: #667eea;
  font-size: 14px;
}

.coupon-discount {
  font-size: 12px;
  color: #10b981;
  font-weight: 600;
}

.copy-btn {
  background: #667eea;
  color: white;
  border: none;
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
  transition: background 0.3s;
}

.copy-btn:hover {
  background: #5568d3;
}

.no-winners {
  text-align: center;
  padding: 60px 20px;
  background: #f9fafb;
  border-radius: 15px;
  margin-bottom: 40px;
}

.no-winners-icon {
  font-size: 64px;
  margin-bottom: 20px;
}

.no-winners p {
  margin: 10px 0;
  color: #6b7280;
}

.hint {
  font-size: 14px;
  font-style: italic;
}

.history-section {
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.history-section h3 {
  margin: 0 0 20px 0;
  font-size: 22px;
  color: #1f2937;
}

.history-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  border-radius: 12px;
  color: white;
  display: flex;
  align-items: center;
  gap: 15px;
}

.stat-icon {
  font-size: 36px;
}

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 12px;
  opacity: 0.9;
}

@media (max-width: 768px) {
  .winners-grid {
    grid-template-columns: 1fr;
  }

  .winner-card {
    flex-direction: column;
  }

  .winner-rewards {
    min-width: auto;
  }

  .month-selector {
    flex-direction: column;
    gap: 15px;
  }

  .current-month {
    min-width: auto;
  }
}
</style>
