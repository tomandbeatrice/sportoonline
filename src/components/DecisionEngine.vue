const suggestAction = (modul: ModulData) => {
  if (modul.coverage < 60 && modul.riskScore > 70) {
    return 'Refactor önerisi: test coverage artırılmalı'
  }
  if (modul.uiDropRate > 30) {
    return 'UI sadeleştirme önerisi: kullanıcı davranışı düşmüş'
  }
  return 'Modül stabil, öneri gerekmez'
}