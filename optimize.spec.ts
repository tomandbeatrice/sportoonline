import { describe, test, expect, vi } from "vitest"
import { debounce, throttle, cleanObject, groupBy, sortBy } from "./utils/optimize"

describe("optimize utils", () => {
  test("cleanObject removes null and empty strings", () => {
    const input = { a: "hello", b: null, c: "", d: 42 }
    const result = cleanObject(input)
    expect(result).toEqual({ a: "hello", d: 42 })
  })

  test("groupBy groups items by key", () => {
    const data = [
      { type: "bug", id: 1 },
      { type: "feature", id: 2 },
      { type: "bug", id: 3 },
    ]
    const grouped = groupBy(data, "type")
    expect(grouped).toEqual({
      bug: [
        { type: "bug", id: 1 },
        { type: "bug", id: 3 },
      ],
      feature: [{ type: "feature", id: 2 }],
    })
  })

  test("sortBy sorts ascending by key", () => {
    const data = [
      { score: 30 },
      { score: 10 },
      { score: 20 },
    ]
    const sorted = sortBy(data, "score")
    expect(sorted).toEqual([
      { score: 10 },
      { score: 20 },
      { score: 30 },
    ])
  })

  test("debounce delays function call", async () => {
    const fn = vi.fn()
    const debounced = debounce(fn, 100)
    debounced()
    debounced()
    await new Promise((r) => setTimeout(r, 150))
    expect(fn).toHaveBeenCalledTimes(1)
  })

  test("throttle limits function calls", async () => {
    const fn = vi.fn()
    const throttled = throttle(fn, 100)
    throttled()
    throttled()
    await new Promise((r) => setTimeout(r, 150))
    throttled()
    expect(fn).toHaveBeenCalledTimes(2)
  })
})