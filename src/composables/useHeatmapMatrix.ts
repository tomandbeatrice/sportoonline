export type HeatmapCell = {
  key: string
  userId: string
  date: string
  count: number
}

export function useHeatmapMatrix(users: UserLogData[], dates: string[]) {
  const matrix: HeatmapCell[] = []

  users.forEach(user => {
    dates.forEach(date => {
      const log = user.logs.find(l => l.date === date)
      matrix.push({
        key: `${user.userId}-${date}`,
        userId: user.userId,
        date,
        count: log?.count || 0,
      })
    })
  })

  return matrix
}