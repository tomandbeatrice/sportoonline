<template>
  <div class="turbo-mode-panel">
    <!-- Header -->
    <div class="turbo-header">
      <div class="turbo-logo">
        <div class="turbo-icon">
          <IconBolt cls="w-6 h-6 text-yellow-400" />
        </div>
        <h2>TURBO MOD</h2>
      </div>
      <div class="turbo-countdown" v-if="competition">
        <div class="countdown-label">Yarışma Bitiş</div>
        <div class="countdown-timer">{{ countdown }}</div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="turbo-loading">
      <div class="spinner"></div>
      <p>Sıralamalar yükleniyor...</p>
    </div>

    <!-- Competition Info -->
    <div v-else-if="competition" class="competition-info">
      <div class="info-card">
        <div class="info-icon">
          <IconTrophy cls="w-6 h-6 text-white" />
        </div>
        <div class="info-content">
          <div class="info-label">Aylık Yarışma</div>
          <div class="info-value">{{ competitionMonth }}</div>
        </div>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <IconGift cls="w-6 h-6 text-white" />
        </div>
        <div class="info-content">
          <div class="info-label">Toplam Ödül</div>
          <div class="info-value">₺7,500</div>
        </div>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <IconTarget cls="w-6 h-6 text-white" />
        </div>
        <div class="info-content">
          <div class="info-label">Katılımcı</div>
          <div class="info-value">{{ totalParticipants }}</div>
        </div>
      </div>
    </div>

    <!-- Leaderboards -->
    <div class="leaderboards" v-if="!loading">
      <!-- Customer Leaderboard -->
      <div class="leaderboard-section">
        <div class="section-header">
          <h3 class="flex items-center gap-2"><IconCart cls="w-5 h-5 text-gray-700" /> En İyi Müşteriler</h3>
          <p class="section-desc">Toplam Alışveriş Tutarı</p>
        </div>

        <div class="podium">
          <!-- 2nd Place -->
          <div class="podium-item rank-2" v-if="topCustomers[1]">
            <div class="podium-rank">
              <IconMedal cls="w-8 h-8" />
            </div>
            <div class="podium-user">
              <div class="user-avatar">{{ getInitials(topCustomers[1].name) }}</div>
              <div class="user-name">{{ topCustomers[1].name }}</div>
              <div class="user-stat">₺{{ formatNumber(topCustomers[1].total_spending) }}</div>
              <div class="user-orders">{{ topCustomers[1].order_count }} Sipariş</div>
              <div class="reward-badge">
                <span class="reward-money">₺500</span>
                <span class="reward-points">+3000 Puan</span>
              </div>
            </div>
            <div class="podium-bar rank-2-bar"></div>
          </div>

          <!-- 1st Place -->
          <div class="podium-item rank-1" v-if="topCustomers[0]">
            <div class="podium-rank">
              <IconMedal cls="w-10 h-10" />
            </div>
            <div class="podium-user">
              <div class="user-avatar gold">{{ getInitials(topCustomers[0].name) }}</div>
              <div class="user-name">{{ topCustomers[0].name }}</div>
              <div class="user-stat">₺{{ formatNumber(topCustomers[0].total_spending) }}</div>
              <div class="user-orders">{{ topCustomers[0].order_count }} Sipariş</div>
              <div class="reward-badge gold">
                <span class="reward-money">₺1,000</span>
                <span class="reward-points">+5000 Puan</span>
              </div>
            </div>
            <div class="podium-bar rank-1-bar"></div>
          </div>

          <!-- 3rd Place -->
          <div class="podium-item rank-3" v-if="topCustomers[2]">
            <div class="podium-rank">
              <IconMedal cls="w-8 h-8" />
            </div>
            <div class="podium-user">
              <div class="user-avatar">{{ getInitials(topCustomers[2].name) }}</div>
              <div class="user-name">{{ topCustomers[2].name }}</div>
              <div class="user-stat">₺{{ formatNumber(topCustomers[2].total_spending) }}</div>
              <div class="user-orders">{{ topCustomers[2].order_count }} Sipariş</div>
              <div class="reward-badge">
                <span class="reward-money">₺250</span>
                <span class="reward-points">+2000 Puan</span>
              </div>
            </div>
            <div class="podium-bar rank-3-bar"></div>
          </div>
        </div>

        <!-- 4th and 5th -->
        <div class="runner-ups">
          <div class="runner-up" v-for="(customer, index) in topCustomers.slice(3, 5)" :key="customer.id">
            <div class="runner-rank">{{ index + 4 }}</div>
            <div class="runner-info">
              <div class="runner-name">{{ customer.name }}</div>
              <div class="runner-stat">₺{{ formatNumber(customer.total_spending) }} • {{ customer.order_count }} Sipariş</div>
            </div>
            <div class="runner-badge">
              <svg class="w-6 h-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Seller Leaderboard -->
      <div class="leaderboard-section">
        <div class="section-header">
          <h3 class="flex items-center gap-2"><svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18v13a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM16 3h-4a2 2 0 00-2 2v2h8V5a2 2 0 00-2-2z"/></svg> En İyi Satıcılar</h3>
          <p class="section-desc">Toplam Satış Geliri</p>
        </div>

        <div class="podium">
          <!-- 2nd Place -->
          <div class="podium-item rank-2" v-if="topSellers[1]">
            <div class="podium-rank">🥈</div>
            <div class="podium-user">
              <div class="user-avatar">{{ getInitials(topSellers[1].name) }}</div>
              <div class="user-name">{{ topSellers[1].name }}</div>
              <div class="user-stat">₺{{ formatNumber(topSellers[1].total_revenue) }}</div>
              <div class="user-orders">{{ topSellers[1].order_count }} Satış</div>
              <div class="reward-badge seller">
                <span class="reward-money">₺1,000</span>
                <span class="reward-points">+6000 Puan</span>
                <span class="reward-coupon">%7 Kupon</span>
              </div>
            </div>
            <div class="podium-bar rank-2-bar"></div>
          </div>

          <!-- 1st Place -->
          <div class="podium-item rank-1" v-if="topSellers[0]">
            <div class="podium-rank">🥇</div>
            <div class="podium-user">
              <div class="user-avatar gold">{{ getInitials(topSellers[0].name) }}</div>
              <div class="user-name">{{ topSellers[0].name }}</div>
              <div class="user-stat">₺{{ formatNumber(topSellers[0].total_revenue) }}</div>
              <div class="user-orders">{{ topSellers[0].order_count }} Satış</div>
              <div class="reward-badge gold seller">
                <span class="reward-money">₺2,000</span>
                <span class="reward-points">+10000 Puan</span>
                <span class="reward-coupon">%10 Kupon</span>
              </div>
            </div>
            <div class="podium-bar rank-1-bar"></div>
          </div>

          <!-- 3rd Place -->
          <div class="podium-item rank-3" v-if="topSellers[2]">
            <div class="podium-rank">🥉</div>
            <div class="podium-user">
              <div class="user-avatar">{{ getInitials(topSellers[2].name) }}</div>
              <div class="user-name">{{ topSellers[2].name }}</div>
              <div class="user-stat">₺{{ formatNumber(topSellers[2].total_revenue) }}</div>
              <div class="user-orders">{{ topSellers[2].order_count }} Satış</div>
              <div class="reward-badge seller">
                <span class="reward-money">₺500</span>
                <span class="reward-points">+4000 Puan</span>
                <span class="reward-coupon">%5 Kupon</span>
              </div>
            </div>
            <div class="podium-bar rank-3-bar"></div>
          </div>
        </div>

        <!-- 4th and 5th -->
        <div class="runner-ups">
          <div class="runner-up" v-for="(seller, index) in topSellers.slice(3, 5)" :key="seller.id">
            <div class="runner-rank">{{ index + 4 }}</div>
            <div class="runner-info">
              <div class="runner-name">{{ seller.name }}</div>
              <div class="runner-stat">₺{{ formatNumber(seller.total_revenue) }} • {{ seller.order_count }} Satış</div>
            </div>
            <div class="runner-badge">
              <svg class="w-6 h-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Position (if logged in) -->
    <div class="my-position" v-if="myPosition && !loading">
      <div class="position-header">
        <h4>Benim Sıralamam</h4>
        <div class="position-rank">#{{ myPosition.rank }}</div>
      </div>
      <div class="position-stats">
        <div class="position-stat">
          <span class="stat-label">Toplam:</span>
          <span class="stat-value">₺{{ formatNumber(myPosition.total) }}</span>
        </div>
        <div class="position-stat">
          <span class="stat-label">Turbo Puan:</span>
          <span class="stat-value">{{ formatNumber(myPosition.turbo_points) }}</span>
        </div>
      </div>
      <div class="position-progress">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: positionProgress + '%' }"></div>
        </div>
        <p class="progress-text">İlk 3'e girmek için devam et! <IconRocket cls="inline w-4 h-4 align-text-bottom" /></p>
      </div>
    </div>

    <!-- Rules -->
    <div class="turbo-rules">
      <h4 class="flex items-center gap-2"><IconClipboard cls="w-5 h-5 text-gray-700" /> Yarışma Kuralları</h4>
      <ul>
        <li><strong>Müşteriler:</strong> Aylık toplam alışveriş tutarına göre sıralanır</li>
        <li><strong>Satıcılar:</strong> Aylık toplam satış gelirine göre sıralanır</li>
        <li><strong>Ödüller:</strong> Her ay ilk 3 sıra para, puan ve kupon kazanır</li>
        <li><strong>Kuponlar:</strong> Satıcılar komisyon indirimi kuponu alır (30 gün geçerli)</li>
        <li><strong>Sıfırlama:</strong> Her ayın 1'inde yarışma sıfırlanır ve yeni dönem başlar</li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import IconBolt from '../icons/IconBolt.vue';
import IconTrophy from '../icons/IconTrophy.vue';
import IconGift from '../icons/IconGift.vue';
import IconTarget from '../icons/IconTarget.vue';
import IconCart from '../icons/IconCart.vue';
import IconMedal from '../icons/IconMedal.vue';
import IconRunner from '../icons/IconRunner.vue';
import IconRocket from '../icons/IconRocket.vue';
import IconClipboard from '../icons/IconClipboard.vue';

export default {
  name: 'TurboMode',
  components: { IconBolt, IconTrophy, IconGift, IconTarget, IconCart, IconMedal, IconRunner, IconRocket, IconClipboard },
  setup() {
    const loading = ref(true);
    const competition = ref(null);
    const topCustomers = ref([]);
    const topSellers = ref([]);
    const myPosition = ref(null);
    const currentTime = ref(new Date());

    let countdownInterval = null;

    const competitionMonth = computed(() => {
      if (!competition.value) return '';
      const months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                      'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
      return `${months[competition.value.month - 1]} ${competition.value.year}`;
    });

    const countdown = computed(() => {
      if (!competition.value) return '';
      
      const endDate = new Date(competition.value.end_date);
      const diff = endDate - currentTime.value;
      
      if (diff <= 0) return 'Sona Erdi';
      
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      
      return `${days}g ${hours}s ${minutes}d`;
    });

    const totalParticipants = computed(() => {
      return topCustomers.value.length + topSellers.value.length;
    });

    const positionProgress = computed(() => {
      if (!myPosition.value || myPosition.value.rank <= 3) return 100;
      return Math.max(10, 100 - (myPosition.value.rank * 5));
    });

    const fetchData = async () => {
      try {
        loading.value = true;

        // Fetch competition stats
        const response = await axios.get('/api/turbo/current');
        
        if (response.data.success) {
          competition.value = response.data.data.current_competition;
          topCustomers.value = response.data.data.top_customers || [];
          topSellers.value = response.data.data.top_sellers || [];
        }

        // Fetch user position if logged in
        const token = localStorage.getItem('token');
        if (token) {
          try {
            const posResponse = await axios.get('/api/turbo/my-position', {
              headers: { Authorization: `Bearer ${token}` }
            });
            if (posResponse.data.success) {
              myPosition.value = posResponse.data.data;
            }
          } catch (err) {
            // User not in rankings yet
            myPosition.value = null;
          }
        }
      } catch (error) {
        console.error('Turbo Mod verileri yüklenemedi:', error);
      } finally {
        loading.value = false;
      }
    };

    const formatNumber = (num) => {
      if (!num) return '0';
      return parseFloat(num).toLocaleString('tr-TR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    };

    const getInitials = (name) => {
      if (!name) return '?';
      return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
    };

    onMounted(() => {
      fetchData();
      
      // Update countdown every minute
      countdownInterval = setInterval(() => {
        currentTime.value = new Date();
      }, 60000);

      // Refresh data every 5 minutes
      const refreshInterval = setInterval(fetchData, 300000);

      onUnmounted(() => {
        clearInterval(countdownInterval);
        clearInterval(refreshInterval);
      });
    });

    return {
      loading,
      competition,
      topCustomers,
      topSellers,
      myPosition,
      competitionMonth,
      countdown,
      totalParticipants,
      positionProgress,
      formatNumber,
      getInitials
    };
  }
};
</script>

<style scoped>
.turbo-mode-panel {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  padding: 30px;
  color: white;
  margin: 20px 0;
  box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
}

.turbo-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.turbo-logo {
  display: flex;
  align-items: center;
  gap: 15px;
}

.turbo-icon {
  font-size: 48px;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

.turbo-logo h2 {
  margin: 0;
  font-size: 36px;
  font-weight: 900;
  letter-spacing: 2px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.turbo-countdown {
  text-align: right;
}

.countdown-label {
  font-size: 12px;
  opacity: 0.8;
  margin-bottom: 5px;
}

.countdown-timer {
  font-size: 24px;
  font-weight: 700;
  font-family: 'Courier New', monospace;
  background: rgba(255, 255, 255, 0.2);
  padding: 10px 20px;
  border-radius: 10px;
}

.competition-info {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  margin-bottom: 30px;
}

.info-card {
  background: rgba(255, 255, 255, 0.15);
  padding: 20px;
  border-radius: 15px;
  display: flex;
  align-items: center;
  gap: 15px;
  backdrop-filter: blur(10px);
}

.info-icon {
  font-size: 32px;
}

.info-label {
  font-size: 12px;
  opacity: 0.8;
}

.info-value {
  font-size: 20px;
  font-weight: 700;
}

.turbo-loading {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  margin: 0 auto 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.leaderboards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 30px;
  margin-bottom: 30px;
}

.leaderboard-section {
  background: rgba(255, 255, 255, 0.1);
  padding: 25px;
  border-radius: 15px;
  backdrop-filter: blur(10px);
}

.section-header {
  text-align: center;
  margin-bottom: 25px;
}

.section-header h3 {
  margin: 0 0 5px 0;
  font-size: 24px;
}

.section-desc {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
}

.podium {
  display: flex;
  justify-content: center;
  align-items: flex-end;
  gap: 15px;
  margin-bottom: 25px;
  min-height: 300px;
}

.podium-item {
  flex: 1;
  text-align: center;
  position: relative;
}

.podium-rank {
  font-size: 40px;
  margin-bottom: 10px;
}

.podium-user {
  background: rgba(255, 255, 255, 0.2);
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 10px;
}

.user-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  font-weight: 700;
  margin: 0 auto 10px;
}

.user-avatar.gold {
  background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
  box-shadow: 0 0 20px rgba(255, 210, 0, 0.5);
  transform: scale(1.1);
}

.user-name {
  font-weight: 700;
  margin-bottom: 5px;
}

.user-stat {
  font-size: 20px;
  font-weight: 700;
  color: #ffd200;
  margin-bottom: 5px;
}

.user-orders {
  font-size: 12px;
  opacity: 0.8;
  margin-bottom: 10px;
}

.reward-badge {
  background: rgba(0, 0, 0, 0.3);
  padding: 8px;
  border-radius: 8px;
  font-size: 11px;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.reward-badge.gold {
  background: rgba(255, 210, 0, 0.3);
  border: 2px solid #ffd200;
}

.reward-badge.seller .reward-coupon {
  color: #4ade80;
  font-weight: 700;
}

.reward-money {
  color: #4ade80;
  font-weight: 700;
}

.reward-points {
  color: #60a5fa;
}

.podium-bar {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 100%);
  border-radius: 10px 10px 0 0;
  margin: 0 10px;
}

.rank-1-bar {
  height: 150px;
  background: linear-gradient(180deg, #ffd200 0%, #f7971e 100%);
}

.rank-2-bar {
  height: 120px;
  background: linear-gradient(180deg, #c0c0c0 0%, #999 100%);
}

.rank-3-bar {
  height: 90px;
  background: linear-gradient(180deg, #cd7f32 0%, #a0522d 100%);
}

.runner-ups {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.runner-up {
  background: rgba(255, 255, 255, 0.1);
  padding: 15px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.runner-rank {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
}

.runner-info {
  flex: 1;
}

.runner-name {
  font-weight: 700;
  margin-bottom: 3px;
}

.runner-stat {
  font-size: 13px;
  opacity: 0.8;
}

.runner-badge {
  font-size: 24px;
}

.my-position {
  background: rgba(255, 255, 255, 0.15);
  padding: 25px;
  border-radius: 15px;
  margin-bottom: 30px;
  backdrop-filter: blur(10px);
}

.position-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.position-header h4 {
  margin: 0;
  font-size: 20px;
}

.position-rank {
  font-size: 32px;
  font-weight: 900;
  color: #ffd200;
}

.position-stats {
  display: flex;
  gap: 30px;
  margin-bottom: 15px;
}

.position-stat {
  display: flex;
  gap: 10px;
  align-items: baseline;
}

.stat-label {
  opacity: 0.8;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #4ade80;
}

.position-progress {
  margin-top: 15px;
}

.progress-bar {
  height: 10px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 10px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #4ade80 0%, #22c55e 100%);
  transition: width 0.3s ease;
}

.progress-text {
  margin: 0;
  font-size: 14px;
  text-align: center;
  opacity: 0.9;
}

.turbo-rules {
  background: rgba(0, 0, 0, 0.2);
  padding: 20px;
  border-radius: 15px;
  backdrop-filter: blur(10px);
}

.turbo-rules h4 {
  margin-top: 0;
  font-size: 18px;
}

.turbo-rules ul {
  margin: 0;
  padding-left: 20px;
}

.turbo-rules li {
  margin-bottom: 8px;
  font-size: 14px;
  line-height: 1.6;
}

@media (max-width: 768px) {
  .turbo-mode-panel {
    padding: 20px;
  }

  .turbo-logo h2 {
    font-size: 24px;
  }

  .turbo-icon {
    font-size: 32px;
  }

  .countdown-timer {
    font-size: 18px;
    padding: 8px 15px;
  }

  .leaderboards {
    grid-template-columns: 1fr;
  }

  .podium {
    min-height: 250px;
  }

  .user-avatar {
    width: 50px;
    height: 50px;
    font-size: 20px;
  }

  .position-stats {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
