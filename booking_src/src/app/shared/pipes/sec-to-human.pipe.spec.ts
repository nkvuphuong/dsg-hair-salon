import { SecToHumanPipe } from './sec-to-human.pipe';

describe('SecToHumanPipe', () => {
  it('create an instance', () => {
    const pipe = new SecToHumanPipe();
    expect(pipe).toBeTruthy();
  });
});
